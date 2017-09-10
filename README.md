# ali/weixin等API调用示例
## 项目运行环境：Linux/CentOs6.5、Apache/2.4、PHP/5.6、MySQL/5.5
##### 此工程借助ThinkPHP3.2.3框架集合了一下API，但任一接口都可以单独抽出使用，不依赖于其他API
关于为何要使用第三方API，会不会有风险？[阿里云API网关产品经理somany做了一些分享](https://yq.aliyun.com/articles/72533?spm=5176.100239.0.0.g6BX1g)，可以移步阅读
## 阿里云市场部分API调用结果优化  
由于阿里云市场的API返回结果没有经过格式化，方便使用；
具体调用的接口可参见[阿里云市场](https://promotion.aliyun.com/ntms/market/data.html?spm=5176.8142029.414693.37.Agg7CB)  
有兴趣可以学习[阿里云大学](https://edu.aliyun.com/)推出的API课程[使用API扩展应用功能](https://edu.aliyun.com/course/69?spm=0.0.0.0.k7BeWl)
## 支付宝支付API调用示例
支付宝使用流程
 1. 先从支付宝商家页面下载证书，放在/ThinkPHP/Library/Vendor/Alipay/目录下；  
 2. 配置/Alipay/Conf/config.php里的相关参数，/Alipay/Common/function.php中也需要填写必要的参数；  
 3. 在/Alipay/Controller/AlipayController.class.php控制器的alipay方法里写你自己的判断允许支付的业务逻辑；
 4. 调用alipay函数(在Alipay/Common/function.php中已经写好，这是可以直接调用)如果没有问题，这是会跳转至支付宝支付页面，页面中会显示支付金额等已经预设好的参数等待支付；  
 5. 用户完成支付操作，支付宝会同时返回异步通知和同步通知，最先抵达的会被服务器接受处理，完成支付流程；  

### 支付宝异步通知和同步通知的差异：

 1. 同步通知会直接跳转至预先配置的回调地址，通过get方式传递支付参数，形如
```
http://www.futurelab.top/ali_return?buyer_email=XXX&buyer_id=2088XXXX&exterface=create_direct_pay_by_user&is_success=T&notify_id=RqPnCoPT3K9%252Fvwbh3InWfjfy%252BgJNrz7BPac%252FqwHxJcac3tW0SBAZc&notify_time=2017-09-010+15%3A25%3A20&notify_type=trade_status_sync&out_trade_no=123456789&payment_type=1&seller_email=XXXX&seller_id=2088XXXX&subject=%E4%BC%98%E8%B0%B1%E5%88%9B%E6%96%B0%E8%AF%BE%E7%A8%8B%2F352436&total_fee=0.01&trade_no=201709010XXX&trade_status=TRADE_SUCCESS&sign=505865055c40964fXXXXX&sign_type=MD5
```
    在多达17个参数里包含了商户订单号、交易金额、交易状态等返回值，可以通过交易状态判断交易是否成功，通过商户订单号参数可以查找商户系统的订单，有必要验证交易金额等参数，判断是否为支付宝直接返回的数据而非被篡改的数据，判断交易成功后即可根据自身业务流程决定是否要将订单置为已支付，将订单中商品增添至用户已购列表，完成交易；  

 2. 异步通知是服务器间的交互，该方式的调试与运行必须在服务器上，即互联网上能访问，不像页面跳转同步通知可以在页面上显示出来，这种交互方式是不可见的。当第一次交易状态改变(即时到账中此时交易状态是交易完成)时，服务器异步通知页面会收到支付宝通过post方式发来的处理结果通知，接受到的参数形如：  
    ```
    {
        "discount":"0.00",
        "payment_type":"1",
        "subject":"支付宝调用体验课程",
        "trade_no":"20170XXXX",
        "buyer_email":"XXXX@163.com",
        "gmt_create":"2017-09-10 15:42:54",
        "notify_type":"trade_status_sync",
        "quantity":"1",
        "out_trade_no":"170910XXXX",
        "seller_id":"2088XXXX",
        "notify_time":"2017-09-10 15:43:32",
        "trade_status":"TRADE_SUCCESS",
        "is_total_fee_adjust":"N",
        "total_fee":"0.01",
        "gmt_payment":"2017-09-10 15:43:32",
        "seller_email":"XXXX",
        "price":"0.01",
        "buyer_id":"2088XXXXX",
        "notify_id":"9a3c80f632XXXXX",
        "use_coupon":"N",
        "sign_type":"MD5",
        "sign":"dd3e4a6e29b677c9dXXXX"
    }
    ```
    接受到参数后处理方式和同步通知相同，但不同的是程序执行完后必须打印输出“success”。如果反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；程序执行完成后，该页面不能执行页面跳转。如果执行页面跳转，支付宝会收不到success字符，会被支付宝服务器判定为该页面程序运行出现异常，而重发处理结果通知；cookies、session等在此页面会失效，即无法使用这些数据。

## 微信支付API调用示例；  
 1. 参数  
 2. 生成带有微信支付参数的二维码  
 3. 心跳  
 4. 微信web端生成的订单和移动端生成的订单视为不同的订单，即订单号不能一致
 5. 只有异步返回

个人观点  
1、对于选择支付宝支付和微信支付的选择  
  支付宝的产品迭代很快，文档、API更新也很快，这意味着你能使用到更好、更快、更安全的支付方式，支付宝的文档几乎是不停在更新，基本上说的详尽明了，给出的demo也能直接运行。对于微信，用一句话就可以总结：文档错漏百出，但万年不更新。根据个人喜好选择支付方式，选择支付方式的另一个参考因素是你用户的支付习惯。


## 微信公众号相关API调用示例(部分功能需要认证)；  
  * 同步微信服务器图文素材  
  * 同步微信服务器图片素材  
  * 设置微信关键词回复  
   1. 没有最多200限制  
   2. 可以任意组合多图文，即使是没有发布过的也可以  
  * 设置微信自定义菜单  
  * 获取微信用户openid，实现微信身份自动登录(只支持认证服务号，订阅号可以变通)  
  * 获取微信用户uuid，实现多平台统一账号(只支持认证服务号,需要绑定微信开放平台开发者账号)  

## 阿里大于和云片API调用示例；  
