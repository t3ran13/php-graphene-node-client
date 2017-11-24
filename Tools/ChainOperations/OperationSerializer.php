<?php

namespace GrapheneNodeClient\Tools\ChainOperations;

use TrafficCophp\ByteBuffer\Buffer;

class OperationSerializer
{
    public static function serializeTransaction($trxParams, $byteBuffer = null) {
        $buffer = $byteBuffer === null ? (new Buffer()) : $byteBuffer;

        $buffer->writeInt16LE($trxParams[0]['ref_block_num'], 0);
        $buffer->writeInt32LE($trxParams[0]['ref_block_prefix'], 2);
        $expirationSec = is_int($trxParams[0]['expiration']) ? $trxParams[0]['expiration'] : strtotime($trxParams[0]['expiration']);
        $buffer->writeInt32LE($expirationSec, 6);
        $buffer->writeInt8(count($trxParams[0]['operations']), 10);

        return $byteBuffer === null ? $buffer->getBuffer('H', 0, $buffer->getCurrentOffset()) : $byteBuffer;
    }


    public static function serializeOperation($operationName, $data, $byteBuffer = null) {
        if ($operationName === ChainOperations::OPERATION_VOTE) {
            return self::serializeOperationVote($data, $byteBuffer);
        }
    }


    public static function serializeOperationVote($data, $byteBuffer = null) {
        $buffer = $byteBuffer === null ? (new Buffer()) : $byteBuffer;

        //operation id
        $opId = ChainOperations::getOperationId(ChainOperations::OPERATION_VOTE);
        $buffer->writeInt8($opId);

        //voter
        $offset = $buffer->getCurrentOffset();
        $buffer->writeInt16LE(strlen($data['voter']));
        $offset += 1;
        $buffer->setCurrentOffset($offset);
        $buffer->writeVStringLE($data['voter'], $offset);

        //author
        $offset = $buffer->getCurrentOffset();
        $buffer->writeInt16LE(strlen($data['author']));
        $offset += 1;
        $buffer->setCurrentOffset($offset);
        $buffer->writeVStringLE($data['author'], $offset);

        //permlink
        $offset = $buffer->getCurrentOffset();
        $buffer->writeInt16LE(strlen($data['permlink']));
        $offset += 1;
        $buffer->setCurrentOffset($offset);
        $buffer->writeVStringLE($data['permlink'], $offset);

        //weight
        $buffer->writeInt16LE($data['weight']);

        return $byteBuffer === null ? $buffer->getBuffer('H', 0, $buffer->getCurrentOffset()) : $byteBuffer;
    }


}