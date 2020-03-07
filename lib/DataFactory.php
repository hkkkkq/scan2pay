<?php
/**
 * Data Factory
 */
Class DataFactory {
    protected $redis;
    protected $cacheDir = __DIR__ . '/../runtime/data/';
    protected $cacheKeyPrefix = 's2p';  //default cache key prefix

    /**
     * config: ['redis' => ['host' => '127.0.0.1', 'port' => 6379, 'secret' => '', 'dbindex' => 0, 'keyPrefix' => '']]
     */
    function __construct($config) {     //--{{{
        //make sure cache directory exist
        if (!is_dir($this->cacheDir)) {
            try {
                mkdir($this->cacheDir, 0755);
            }catch(Exception $e) {
                throw new Exception("Cache directory create failed, please make sure you have write permission of directory: {$this->cacheDir}");
            }
        }

        //redis init
        if (class_exists('Redis')) {
            $redisConfig = $config['redis'];

            $this->redis = new Redis();
            $this->redis->connect($redisConfig['host'], $redisConfig['port']);
            if (!empty($redisConfig['secret'])) {
                $this->redis->auth($redisConfig['secret']);
            }
            if (!empty($redisConfig['dbindex'])) {
                $this->redis->select($redisConfig['dbindex']);
            }
            if (!empty($redisConfig['keyPrefix'])) {
                $this->cacheKeyPrefix = $redisConfig['keyPrefix'];
            }
        }else {
            throw new Exception("Redis extension not installed!");
        }
    }   //--}}}

    function __destruct() {     //--{{{
        $this->redis->close();
    }   //--}}}

    //get local cache file path
    //@date: YYmmdd
    protected function getCacheFile($date) {    //--{{{
        return $this->cacheDir . (int)$date . '.json';
    }   //--}}}

    protected function append2file($date, $data) {  //--{{{
        try {
            $cacheFile = $this->getCacheFile($date);
            $handle = fopen($cacheFile, 'a');
            fwrite($handle, json_encode($data) . "\n");
            fclose($handle);
        }catch(Exception $e) {
            throw new Exception("Fail to save data into file: {$cacheFile}.\nError detail: " . $e->getMessage());
        }
    }   //--}}}

    protected function getDataFromCacheFile($date, $index = -1) {       //--{{{
        $data = [];

        try {
            $cacheFile = $this->getCacheFile($date);
            if (!file_exists($cacheFile)) {
                throw new Exception("Data from file: {$cacheFile} not exist.");
            }

            $handle = fopen($cacheFile, 'r');

            while(!feof($handle)) {
                $json = fgets($handle);
                if (empty($json)) {continue;}
                $arr = json_decode($json, true);

                //if get one row, get the last one
                if ($index >= 0 && !empty($arr) && $arr['index'] == $index) {
                    $data = $arr;
                }else if ($index == -1) {   //if get all
                    $data[$arr['index']] = $arr;
                }
            }

            fclose($handle);
        }catch(Exception $e) {
            throw new Exception("Fail to read data from file: {$cacheFile}.\nError detail: " . $e->getMessage());
        }

        return $data;
    }   //--}}}

    //get cache key for redis
    protected function getCacheKey4Redis($date) {   //--{{{
        return $this->cacheKeyPrefix . ':' . (int)$date;
    }   //--}}}

    //save data into redis
    protected function save2redis($date, $index, $data) {       //--{{{
        //save into redis
        try {
            $cacheKey = $this->getCacheKey4Redis($date);
            $this->redis->zRemRangeByScore($cacheKey, $index, $index);  //try to remove
            $this->redis->zAdd($cacheKey, $index, json_encode($data));  //try to add
        }catch(Exception $e) {
            throw new Exception("Fail to save data into redis: " . $e->getMessage());
        }
    }       //--}}}

    //get cache data directory
    public function getCacheDir() {    //--{{{
        return $this->cacheDir;
    }   //--}}}

    //append data to sorted sets and local cache file
    //@date: YYmmdd
    //@index: int
    //@data: array
    public function save($date, $index, $data) {      //--{{{
        if (empty($data['index'])) {
            $data['index'] = $index;
        }

        try {
            //save into cache file
            $this->append2file($date, $data);

            //save into redis
            $this->save2redis($date, $index, $data);
        }catch(Exception $e) {
            throw $e;
        }

        return $data;
    }   //--}}}

    //get all data from redis sorted sets and local cache file
    //@date: YYmmdd
    public function getAll($date) {     //--{{{
        $data = [];

        try {
            $cacheKey = $this->getCacheKey4Redis($date);
            $withscores = true;
            $arr = $this->redis->zRange($cacheKey, 0, -1, $withscores);
            if (!empty($arr)) {
                foreach ($arr as $json => $index) {
                    $data[$index] = json_decode($json, true);
                }
            }
            if (empty($data)) {
                $data = $this->getDataFromCacheFile($date);

                //save back into redis
                if (!empty($data)) {
                    foreach ($data as $item) {
                        $this->save2redis($date, $item['index'], $item);
                    }
                }
            }
        }catch(Exception $e){}

        return $data;
    }   //--}}}

    //get one data from redis sorted sets and local cache file
    //@date: YYmmdd
    //@index: int
    public function get($date, $index) {    //--{{{
        $data = [];

        try {
            $cacheKey = $this->getCacheKey4Redis($date);
            $arr = $this->redis->zRangeByScore($cacheKey, $index, $index);
            if (!empty($arr)) {
                foreach ($arr as $json) {
                    $data = json_decode($json, true);
                    break;
                }
            }
            if (empty($data)) {
                $data = $this->getDataFromCacheFile($date, $index);
            }
        }catch(Exception $e){}

        return $data;
    }   //--}}}

}
