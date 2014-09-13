<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 14-9-14
 * Time: 1:35
 */

namespace Qiniu;


class Qiniu_RSF {

    const Qiniu_RSF_EOF = 'EOF';

    /**
     * 1. 首次请求 marker = ""
     * 2. 无论 err 值如何，均应该先看 items 是否有内容
     * 3. 如果后续没有更多数据，err 返回 EOF，markerOut 返回 ""（但不通过该特征来判断是否结束）
     *
     * return // => ($items, $markerOut, $err)
     */
    static function Qiniu_RSF_ListPrefix( $self, $bucket, $prefix = '', $marker = '', $limit = 0 ){

        $query = array('bucket' => $bucket);
        if (!empty($prefix)) {
            $query['prefix'] = $prefix;
        }
        if (!empty($marker)) {
            $query['marker'] = $marker;
        }
        if (!empty($limit)) {
            $query['limit'] = $limit;
        }

        $url =    Conf::$QINIU_RSF_HOST  . '/list?' . http_build_query($query);
        list($ret, $err) = Qiniu_Client_Call($self, $url);
        if ($err !== null) {
            return array(null, '', $err);
        }

        $items = $ret['items'];
        if (empty($ret['marker'])) {
            $markerOut = '';
            $err = static::Qiniu_RSF_EOF;
        } else {
            $markerOut = $ret['marker'];
        }
        return array($items, $markerOut, $err);
    }
} 