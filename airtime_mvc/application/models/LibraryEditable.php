<?php

interface Application_Model_LibraryEditable
{

    public function setMetadata($key, $val);
    public function setName($name);
    public function getLength();
    public function getId();
}
