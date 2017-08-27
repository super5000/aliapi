<?php
namespace Weixin\Controller;
use Think\Controller;
class IndexController extends Controller {

  // 验证微信服务器
  public function valid($echoStr) {
    //valid signature , option
    if($this->checkSignature()){
      echo $echoStr;
      exit;
    }
  }

  // 验证微信服务器参数合法性
  private function checkSignature() {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    $token = C('TOKEN');
    $tmpArr = array($token, $timestamp, $nonce);
    // use SORT_STRING rule
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    if( $tmpStr == $signature ){
      return true;
    }else{
      return false;
    }
  }

  public function response(){
    $echoStr = $_GET["echostr"];
    if ($echoStr) {
      $this->valid($echoStr);
    }
    //get post data, May be due to the different environments
    $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    $filename = 'file.txt';
    file_put_contents($filename, "\n".json_encode($postStr), FILE_APPEND);
    //extract post data
    if (!empty($postStr)){
      $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
      $RX_TYPE = trim($postObj->MsgType);
      if ($RX_TYPE == 'event' && ($postObj->Event != 'CLICK')) {
        echo $resultStr = $this->handleEvent($postObj);
      }else {
        if ($RX_TYPE == 'event') {
          $keyword = trim($postObj->EventKey);
        }else {
          $keyword = trim($postObj->Content);
        }
        if(!empty( $keyword )){
          // 判断是否是关键词
          $result = M()->query("SELECT * FROM lab_auto_reply where keyword = '$keyword' order by id desc limit 0,1");
          if (!count($result)) {
            // 未查到关键词
            $result = M()->query("SELECT * FROM lab_auto_reply where msg_type = 'not_set' order by id desc limit 0,1");
          }
          if (($result[0]['msg_type'] == 'text') || ($result[0]['msg_type'] == 'not_set')) {
            $contentStr = $result[0]['content'];
            $resultStr = $this->_response_text($postObj,$contentStr);
          }elseif ($result[0]['msg_type'] == 'news') {
            $news_id = $result[0]['group_id'];
            $news = M()->query(
              "SELECT lab_material_news.*, lab_picture.path
              from lab_material_news
              left join lab_picture on lab_material_news.cover_id = lab_picture.id
              where lab_material_news.group_id = '$news_id'");
            for ($i=0; $i < count($news); $i++) {
              $record[$i]['title'] = $news[$i]['title'];
              $record[$i]['description'] = $news[$i]['intro'];
              $record[$i]['picUrl'] = WORK_PATH.$news[$i]['path'];
              $record[$i]['url'] = $news[$i]['url'];
            }
            if (count($record) > 1) {
              $resultStr = $this->_response_multiNews($postObj,$record);
            }else {
              $resultStr = $this->_response_news($postObj,$record[0]);
            }
          }elseif ($result[0]['msg_type'] == 'image') {
            $imageTpl ="<xml>
          				<ToUserName><![CDATA[%s]]></ToUserName>
          				<FromUserName><![CDATA[%s]]></FromUserName>
          				<CreateTime>%s</CreateTime>
          				<MsgType><![CDATA[%s]]></MsgType>
                  <Image>
                  <MediaId><![CDATA[%s]]></MediaId>
                  </Image>
          				<FuncFlag>0</FuncFlag>
          				</xml>";
            $msgType = "image";
            $media_id = $result[0]['image_material'];
            $image = M()->query("SELECT media_id from lab_material_image where id = '$media_id'");
            $mediaId = $image[0]['media_id'];
            $resultStr = sprintf($imageTpl, $postObj->FromUserName, $postObj->ToUserName, time(), $msgType, $mediaId);
          }
          echo $resultStr;
        }else{
          echo "Input something...";
        }
      }
    }else {
      echo "";
      exit;
    }
    die;
  }

  // 回复文本
  function _response_text($object,$content){
  	$textTpl = "<xml>
  				<ToUserName><![CDATA[%s]]></ToUserName>
  				<FromUserName><![CDATA[%s]]></FromUserName>
  				<CreateTime>%s</CreateTime>
  				<MsgType><![CDATA[text]]></MsgType>
  				<Content><![CDATA[%s]]></Content>
  				<FuncFlag>%d</FuncFlag>
  				</xml>";
  	$resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
  	return $resultStr;
  }
  public function handleEvent($object){
    $contentStr = "";
    switch ($object->Event){
      case "subscribe":
        $result = M()->query("SELECT * FROM lab_auto_reply where msg_type = 'wecome' order by id desc limit 0,1");
        $contentStr = $result[0]['content'];
        break;
      default :
        $contentStr = "Unknow Event: ".$object->Event;
        break;
    }
    $resultStr = $this->_response_text($object, $contentStr);
    return $resultStr;
  }
  // 回复单图文
  function _response_news($object,$newsContent){
  	$newsTplHead = "<xml>
        				    <ToUserName><![CDATA[%s]]></ToUserName>
        				    <FromUserName><![CDATA[%s]]></FromUserName>
        				    <CreateTime>%s</CreateTime>
        				    <MsgType><![CDATA[news]]></MsgType>
        				    <ArticleCount>1</ArticleCount>
        				    <Articles>";
  	$newsTplBody = "<item>
        				    <Title><![CDATA[%s]]></Title>
        				    <Description><![CDATA[%s]]></Description>
        				    <PicUrl><![CDATA[%s]]></PicUrl>
        				    <Url><![CDATA[%s]]></Url>
        				    </item>";
  	$newsTplFoot = "</Articles>
          					<FuncFlag>0</FuncFlag>
          				  </xml>";
  	$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time());
  	$title = $newsContent['title'];
  	$desc = $newsContent['description'];
  	$picUrl = $newsContent['picUrl'];
  	$url = $newsContent['url'];
  	$body = sprintf($newsTplBody, $title, $desc, $picUrl, $url);
  	$FuncFlag = 0;
  	$footer = sprintf($newsTplFoot, $FuncFlag);
  	return $header.$body.$footer;
  }
  // 回复多图文
  function _response_multiNews($object,$newsContent){
  	$newsTplHead = "<xml>
  				    <ToUserName><![CDATA[%s]]></ToUserName>
  				    <FromUserName><![CDATA[%s]]></FromUserName>
  				    <CreateTime>%s</CreateTime>
  				    <MsgType><![CDATA[news]]></MsgType>
  				    <ArticleCount>%s</ArticleCount>
  				    <Articles>";
  	$newsTplBody = "<item>
  				    <Title><![CDATA[%s]]></Title>
  				    <Description><![CDATA[%s]]></Description>
  				    <PicUrl><![CDATA[%s]]></PicUrl>
  				    <Url><![CDATA[%s]]></Url>
  				    </item>";
  	$newsTplFoot = "</Articles>
  					<FuncFlag>0</FuncFlag>
  				    </xml>";
  	$bodyCount = count($newsContent);
  	$bodyCount = $bodyCount < 10 ? $bodyCount : 10;
  	$header = sprintf($newsTplHead, $object->FromUserName, $object->ToUserName, time(), $bodyCount);
  	foreach($newsContent as $key => $value){
  		$body .= sprintf($newsTplBody, $value['title'], $value['description'], $value['picUrl'], $value['url']);
  	}
  	$FuncFlag = 0;
  	$footer = sprintf($newsTplFoot, $FuncFlag);
  	return $header.$body.$footer;
  }



}
