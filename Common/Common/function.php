<?php

// 生成32位uuid
function create_uuid(){
  return $uuid = md5(uniqid(mt_rand(), true));
}
