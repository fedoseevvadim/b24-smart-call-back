<?php
namespace SmartCallBack;

interface CRMStruct {

    public function getStruct (): array;
    public function add(array $array ): int;

}