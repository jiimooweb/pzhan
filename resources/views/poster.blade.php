<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<style>
    body{
        background: #000;
        padding:0;
        margin: 0;
    }

    .content {
        width: 100%;
    }

    .wapper {
        width: 300px;     
        display: flex;
        flex-direction: column;
    }

    .img {
        width: 100%;
        height: auto;
    }

    .info {
        width: 100%;
        /* height: 150px; */
        padding: 15px;
        box-sizing: border-box;
        background-color: #fff;

    }

    .title {
        color:#606266;
        font-size: 14px;
        text-align: center;
        font-size: 200;
    }

    .mini {
        margin-top: 7px;
        padding:0 16px 0 16px;
        background-color: #fff;        
    }

    .qrcode {
        height: 90px;
        width: 90px;
        float: left;
    }

    .tips {
        float: right;
        height: 100%;
        margin-top: 18px;
        margin-left: 15px;        
    }

    .logo {
        height: 25px;
        width: auto;
        
    }
    
    .tip {
        font-size: 12px;
        font-weight: 500;
        color: #67C23A;
    }

</style>
<body>
    <div class="content">
        <div class="wapper">
            <img class="img" src="{{$picture->url}}">
            <div class="info">
                <div class="title">
                    {{$picture->title}} 
                </div>
                <div class="mini">
                    <img class="qrcode" src="https://download.rdoorweb.com/pzhan/gh_061d94cac93e_258.jpg" alt="">
                    <div class="tips">
                        <img class="logo" src="https://download.rdoorweb.com/pzhan/pz_logo.png" alt="">
                        <div class="tip">长按识别免费下载该图片</div>
                    </div>
                </div>
            </div>
        </div>  
        
    </div>
</body>
</html>