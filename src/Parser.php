<?php
namespace Jiemo\Mobile;

class Parser
{
    private $fileHandle = null;
    private $fileSize = 0;
    private $version = 0;

    const ISP_TYPE = [
        1 => '移动',
        2 => '联通',
        3 => '电信',
        4 => '电信虚拟运营商',
        5 => '联通虚拟运营商',
        6 => '移动虚拟运营商',
    ];

    public function __construct()
    {
        $filePath = __DIR__ . '/phone.dat';
        $this->fileHandle = fopen($filePath, 'rb');
        $this->fileSize = filesize($filePath);
    }

    /**
     * 无符号32位
     *
     * @param mixed $data
     * @return integer
     * @author Cong Peijun<me@congpeijun.com>
     */
    private function uInt32($data): int
    {
        return unpack('L', $data)[1];
    }

    /**
     * 无符号8位
     *
     * @param mixed $data
     * @return integer
     * @author Cong Peijun<me@congpeijun.com>
     */
    private function uInt8($data): int
    {
        return unpack('C', $data)[1];
    }

    /**
     * 获取电话号码数据库版本
     *
     * @return integer
     * @author Cong Peijun<me@congpeijun.com>
     */
    public function getDbVersion(): int
    {
        if ($this->version) {
            return $this->version;
        }
        fseek($this->fileHandle, 0);
        return $this->version = fread($this->fileHandle, 4);
    }

    /**
     * 获取索引开始位置
     *
     * @return integer
     * @author Cong Peijun<congpeijun@jiemo.net>
     */
    private function getIndexStart(): int
    {
        fseek($this->fileHandle, 4);
        return $this->uInt32(fread($this->fileHandle, 4));
    }

    /**
     * 解析手机号码, 未找到返回 false, 如果找到记录返回信息数组
     *
     * @param string $mobile
     * @return boolean|array
     * @author Cong Peijun<me@congpeijun.com>
     */
    public function parse(string $mobile)
    {
        $fp = $this->fileHandle;
        $indexStart = $this->getIndexStart();
        $prefix = substr($mobile, 0, 7);

        $start = 0;
        $end = ($this->fileSize - $indexStart) / 9;

        while ($start < $end) {
            $mid = floor(($end - $start) / 2) + $start;
            fseek($fp, $indexStart + $mid * 9);
            $indexPrefix = $this->uInt32(fread($fp, 4));
            if ($prefix < $indexPrefix) {
                $end = $mid - 1;
            } elseif ($prefix > $indexPrefix) {
                $start = $mid + 1;
            } else {
                $dataOffset = $this->uInt32(fread($fp, 4));
                $cardType = $this->uInt8(fread($fp, 1));
                fseek($fp, $dataOffset);
                $current = null;
                $data = '';
                while ($current !== "\0") {
                    $current = fread($fp, 1);
                    $data .= $current;
                }
                $data = explode('|', trim($data));
                return [
                    'province' => $data[0],
                    'city' => $data[1],
                    'zip_code' => $data[2],
                    'area_code' => $data[3],
                    'isp' => self::ISP_TYPE[$cardType],
                ];
            }
        }
        return false;
    }

    public function __destruct()
    {
        fclose($this->fileHandle);
    }
}
