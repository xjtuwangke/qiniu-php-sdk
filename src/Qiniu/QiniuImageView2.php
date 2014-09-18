<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-13
 * Time: 23:17
 */

namespace Qiniu;


class QiniuImageView2 {

    public $Mode = 0;
    public $Width;
    public $Height;
    public $Quality;
    public $Format;

    public function MakeRequest($url)
    {
        $ops[] = $this->Mode;
        if (!empty($this->Width)) {
            $ops[] = 'w/' . $this->Width;
        }
        if (!empty($this->Height)) {
            $ops[] = 'h/' . $this->Height;
        }
        if (!empty($this->Quality)) {
            $ops[] = 'q/' . $this->Quality;
        }
        if (!empty($this->Format)) {
            $ops[] = 'format/' . $this->Format;
        }
        return $url . "?imageView2/" . implode('/', $ops);
    }
} 