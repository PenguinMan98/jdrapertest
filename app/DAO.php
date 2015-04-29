
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66
67
68
69
70
71
72
73
74
75
76
77
78
79
80
81
82
83
84
85
86
87
88
89
90
91
92
93
94
95
96
97
98
99
100
101
102
103
104
105
106
107
108
109
110
111
112
113
114
115
116
117
118
119
120
121
122
123
124
125
126
127
128
129
130
131
132
133
134
135
136
137
138
139
140
141
142
143
144
145
146
147
148
149
150
151
152
153
154
155
156
157
158
159
160
161
162
163
164
165
166
167
168
169
170
171
172
173
174
175
176
177
178
179
180
181
182
183
184
185
186
187
188
189
190
191
192
193
194
195
196
197
198
199
200
201
202
203
204
205
206
207
208
209
210
211
212
213
214
215
216
217
218
219
220
221
222
223
224
225
226
227
228
229
230
231
232
233
234
235
236
237
238
239
240
241
242
243
244
245
246
247
248
249
250
251
252
253
254
255
256
257
258
259
260
261
262
263
264
265
266
267
268
269
270
271
272
273
274
275
276
277
278
279
280
281
282
283
284
285
286
287
288
289
290
291
292
293
294
295
296
297
298
299
300
301
302
303
304
305
306
307
308
309
310
311
312
313
314
315
316
317
318
319
320
321
322
323
324
325
326
327
328
329
330
331
332
333
334
335
336
337
338
339
340
341
342
343
344
345
346
347
348
349
350
351
352
353
354
355
356
357
358
359
360
361
	
<?php
define('DAO_DEBUG', true);
define('DAO_PROFILE', false);
require_once('DbCn.php');
class Dao
{
    const SLOW_QUERY_LIMIT = 5.00;
 
    private static $_intQueryCount = 0;
    private static $_dblQueryTime = 0;
    private static $_dblFetchTime = 0;
 
    public static function getQueryCount()
    {
        return(self::$_intQueryCount);
    }
 
    public static function getQueryTime()
    {
        return(self::$_dblQueryTime);
    }
 
    public static function getFetchTime()
    {
        return(self::$_dblFetchTime);
    }
 
    private static function getCn()
    {
        static $objDbCn;
 
        if (!(isset($objDbCn))) {
            $objDbCn = DbCn::getInstance();
            if ($objDbCn === null) {
                include($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/error.phtml');
                header('Internal Server Error', TRUE, 500);
                exit(1);
            }
        }
 
        return($objDbCn);
    }
 
    public static function getAssoc($strSql, $arrParams, &$arrResults, &$arrErrors)
    {
        $arrResults = array();
        $objDbCn = self::getCn();
         
        // Prepare the actual text of the query
        $objRes = $objDbCn->prepare($strSql);
        $strQueryString = self::getQueryString($objDbCn, $strSql, $arrParams);
        if (DAO_PROFILE) {
            self::logQuery($strQueryString);
        }
        //echo $strQueryString . '<br>';
         
        // Execute the query
        self::$_intQueryCount++;
        $dblStart = microtime(true);
        try {
            $objRes->execute($arrParams);
        }catch (Exception $e) {
            $arrErrors = $objDbCn->errorInfo();
            self::logError(implode("|",$arrErrors), $strQueryString);
            return(false);
        }
        $dblEnd = microtime(true);
        self::$_dblQueryTime += ($dblEnd - $dblStart);
        self::logSlowQuery($strQueryString, ($dblEnd - $dblStart));
 
        if ($objRes->errorCode() != "00000") {
            $arrErrors = $objRes->errorInfo();
            self::logError(implode("|",$arrErrors), $strQueryString);
            return(false);
        }
         
        $objRes->setFetchMode(PDO::FETCH_ASSOC);
        // Get the results into an array
        $dblStart = microtime(true);
        while ($arrLine = $objRes->fetch()) {
            $arrResults[] = $arrLine;
        }
        $dblEnd = microtime(true);
        self::$_dblFetchTime += ($dblEnd - $dblStart);
        //@$objRes->free_result();
 
        return(true);
    }
     
    public static function getMultipleResults($strSql, $arrParams, &$multiResults, &$arrErrors)
    {
        $multiResults = array();
        $objDbCn = self::getCn();
 
        // Prepare the actual text of the query
        $strQueryString = self::getQueryString($objDbCn, $strSql, $arrParams);
        if (DAO_PROFILE) {
            self::logQuery($strQueryString);
        }
 
        // Execute the query
        self::$_intQueryCount++;
        $dblStart = microtime(true);
        $objRes = @$objDbCn->multi_query($strQueryString);
        $dblEnd = microtime(true);
        self::$_dblQueryTime += ($dblEnd - $dblStart);
        if ($objRes === false) {
            $strMessage = mysqli_error($objDbCn);
            $arrErrors[] = 'Error: ' . $strMessage;
            self::logError($strMessage, $strQueryString);
            return(false);
        }
        self::logSlowQuery($strQueryString, ($dblEnd - $dblStart));
 
        // Get the results into an array
        $dblStart = microtime(true);
        do {
            if ($result = @$objDbCn->use_result()) {
                $arrResults = array();
                while ($arrLine = $result->fetch_assoc()) {
                    $arrResults[] = $arrLine;
                }
                $result->close();
                $multiResults[] = $arrResults;
            }
        } while (@$objDbCn->next_result());
        $dblEnd = microtime(true);
        self::$_dblFetchTime += ($dblEnd - $dblStart);
        return(true);
    }
 
    public static function execute($strSql, $arrParams, &$arrErrors)
    {
        $objDbCn = self::getCn();
 
        // Prepare the actual text of the query
        $strQueryString = self::getQueryString($objDbCn, $strSql, $arrParams);
        if (DAO_PROFILE) {
            self::logQuery($strQueryString);
        }
        $blnResult = $objDbCn->prepare($strSql);
         
        // Execute the query
        self::$_intQueryCount++;
        $dblStart = microtime(true);
        try {
            $blnResult->execute($arrParams);
             
        }catch (Exception $e) {
            $arrErrors = $objDbCn->errorInfo();
            self::logError(implode("|",$arrErrors), $strQueryString);
            return(false);
        }
        $dblEnd = microtime(true);
        self::$_dblQueryTime += ($dblEnd - $dblStart);
        if ($blnResult->errorCode() != "00000") {
            $arrErrors = $blnResult->errorInfo();
            self::logError(implode("|",$arrErrors), $strQueryString);
            return(false);
        }
        self::logSlowQuery($strQueryString, ($dblEnd - $dblStart));
 
        return(true);
    }
 
    private static function getQueryString($objDbCn, $strSql, $arrParams = null)
    {
        // If there aren't any parameters, then just return the base query string
        if ($arrParams == null) { return($strSql); }
 
        // Make sure all of the parameters are safe to use
        $intParamCount = count($arrParams);
        for ($i = 0; $i < $intParamCount; $i++) {
            if ($arrParams[$i] === null) {
                $arrParams[$i] = 'NULL';
            }
            elseif ($arrParams[$i] === 'CURRENT_TIMESTAMP') {
                // Deliberately empty; no need to surround this in quotes
            }
            elseif (!(is_numeric($arrParams[$i])) || is_string($arrParams[$i])) {
                $arrParams[$i] = "'" . addslashes($arrParams[$i]) . "'";
            }
        }
 
        // Now replace each ? parameter place holder with the proper corresponding value
        $arrPieces = explode('?', $strSql);
        $strQueryString = $arrPieces[0];
        $intPieceCount = count($arrPieces);
        for ($i = 1; $i < $intPieceCount; $i++) {
            $strQueryString .= $arrParams[$i - 1] . $arrPieces[$i];
        }
 
        // Return the final database query
        return($strQueryString);
    }
 
    private static function logQuery($strSql)
    {
        echo '<div style="width: 800px; text-align: left;">', microtime(true), ' ', $strSql, '</div><br />';
        return;
    }
 
    private static function logError($strMessage, $strQueryString)
    {
        // Trim all extraneous whitespace from our query
        $strQueryString = str_replace(array("\r", "\n", "\t"), ' ', $strQueryString);
        $strQueryString = preg_replace("/\s\s+/", ' ', $strQueryString);
        $strQueryString = trim($strQueryString);
 
        syslog(LOG_ERR, 'PHP Dao: ' . $strMessage . ' : ' . $strQueryString);
 
        if (DAO_DEBUG) {
            echo 'Dao Error: ', $strMessage, '<br />';
        }
 
        return;
    }
 
    private static function logSlowQuery($strQueryString, $dblQueryTime)
    {
        if (self::SLOW_QUERY_LIMIT >= 0 && $dblQueryTime > self::SLOW_QUERY_LIMIT) {
            // Trim all extraneous whitespace from our query
            $strQueryString = str_replace(array("\r", "\n", "\t"), ' ', $strQueryString);
            $strQueryString = preg_replace("/\s\s+/", ' ', $strQueryString);
            $strQueryString = trim($strQueryString);
 
            syslog(LOG_ERR, 'PHP Dao: Slow Query [' . number_format($dblQueryTime, 2) . ' second(s)] : ' . $strQueryString);
        }
 
        return;
    }
 
    public static function getArrayFromObjectList($arrList)
    {
        $arrData = array();
        foreach ($arrList as $objRecord) {
            $arrData[] = $objRecord->getAsArray();
        }
 
        return($arrData);
    }
 
    public static function getInsertId()
    {
        $objDbCn = self::getCn();
        return($objDbCn->lastInsertId());
    }
 
 
    public static function getAssocServer($strSql, $arrParams, &$arrResults, &$arrErrors, $connectionName)
    {
        $arrResults = array();
        $objDbCn = self::getCnServer($connectionName);
 
        // Prepare the actual text of the query
        $strQueryString = self::getQueryString($objDbCn, $strSql, $arrParams);
        if (DAO_PROFILE) {
            self::logQuery($strQueryString);
        }
 
        // Execute the query
        self::$_intQueryCount++;
        $dblStart = microtime(true);
        $objRes = @$objDbCn->query($strQueryString);
        $dblEnd = microtime(true);
        self::$_dblQueryTime += ($dblEnd - $dblStart);
        if ($objRes === false) {
            $strMessage = mysqli_error($objDbCn);
            $arrErrors[] = 'Error: ' . $strMessage;
            self::logError($strMessage, $strQueryString);
            return(false);
        }
        self::logSlowQuery($strQueryString, ($dblEnd - $dblStart));
 
        // Get the results into an array
        $dblStart = microtime(true);
        while ($arrLine = $objRes->fetch_assoc()) {
            $arrResults[] = $arrLine;
        }
        $dblEnd = microtime(true);
        self::$_dblFetchTime += ($dblEnd - $dblStart);
        @$objRes->free_result();
 
        return(true);
    }
 
    public static function executeServer($strSql, $arrParams, &$arrErrors, $connectionName)
    {
        $objDbCn = self::getCnServer($connectionName);
 
        // Prepare the actual text of the query
        $strQueryString = self::getQueryString($objDbCn, $strSql, $arrParams);
        if (DAO_PROFILE) {
            self::logQuery($strQueryString);
        }
 
        // Execute the query
        self::$_intQueryCount++;
        $dblStart = microtime(true);
        $blnResult = @$objDbCn->real_query($strQueryString);
        $dblEnd = microtime(true);
        self::$_dblQueryTime += ($dblEnd - $dblStart);
        if ($blnResult === false) {
            $strMessage = mysqli_error($objDbCn);
            $arrErrors[] = 'Error: ' . $strMessage;
            self::logError($strMessage, $strQueryString);
            return(false);
        }
        self::logSlowQuery($strQueryString, ($dblEnd - $dblStart));
 
        return(true);
    }
 
    private static function getCnServer($connectionName)
    {
        static $objDbCn;
 
        if (!(isset($objDbCn))) {
            $objProvider = DbCn::getInstance();
            $objDbCn = $objProvider->getMySqliCn($connectionName);
            if ($objDbCn === null) {
                include($_SERVER['DOCUMENT_ROOT'] . '/admin/templates/error.phtml');
                header('Internal Server Error', TRUE, 500);
                exit(1);
            }
            /*if (::getIsProduction()) {
                // Reset the database to be in the same timezone as the web server(s)
                $blnResult = @$objDbCn->real_query('SET time_zone=MST7MDT');
                if (!($blnResult)) {
                    die('Error #2.');
                }
            }*/
        }
 
        return($objDbCn);
    }
 
    public static function getInsertIdServer($connectionName)
    {
        $objDbCn = self::getCnServer($connectionName);
        return(mysqli_insert_id($objDbCn));
    }
     
    public static function startTransaction()
    {
        $objDbCn = self::getCn();
        $objDbCn->beginTransaction();
    }
     
    public static function commit()
    {
        $objDbCn = self::getCn();
        $objDbCn->commit();
    }
     
    public static function rollback()
    {
        $objDbCn = self::getCn();
        return $objDbCn->rollback();
    }
}

