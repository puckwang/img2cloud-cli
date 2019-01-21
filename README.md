Img2Cloud CLI
===
一個用於文字介面上傳圖片至 imgur 的工具 for MacOS

## Requirement
* PHP version "^7.1.3" or greater
    
## Install
1. 首先你必須將[執行檔](https://github.com/puckwang/img2cloud-cli/releases/download/v0.2/img2cloud)下載下來，並放置於 `/usr/local/bin/` 中，以方便未來在任意地方都可以執行
```
wget https://github.com/s9801077/img2cloud-cli/releases/download/v0.1/img2cloud
cp img2cloud /usr/local/bin/img2cloud
```


2. 因為 imgur 所提供的 API 有頻寬限制，所以 Img2Cloud 預設沒有 Client ID 與 Secret，你必須去[申請](https://api.imgur.com/oauth2/addclient)取得自己的 Client ID 與 Secret，
如果你沒有 Imgur 帳號，也請去[註冊](https://imgur.com/register?redirect=%2F)。

拿到 Client ID 與 Secret 後請執行 `img2cloud auth`，並依找指示輸入
```
$ img2cloud auth

請至 Imgur 申請一組 API Client ID 與 Client Secret。
> https://api.imgur.com/oauth2/addclient

Client ID: :
> d*************9

Client Secret: :
> 21***********************************16c
```

3. Img2Cloud 沒有開放匿名上傳，所以你必須登入你的 imgur

接下請依照指示去登入，登入後再輸入轉跳後的網址。

```
請至這個連結登入Imgur:
> https://api.imgur.com/oauth2/authorize?client_id=d*************9&response_type=token

請輸入登入後的網址: :
> https://imgur.com/#access_token=5***********************************d&expires_in=315360000&token_type=bearer&refresh_token=7***********************************2&account_username=PuckWang&account_id=7******1
```

4. 如果沒有問題就會出現如下畫面
```
Successful!
```

## Usage Upload
1. 可以使用 `img2cloud uploa` 來上傳圖片，加上 `-h` 來查看參數說明。
macOS 可以使用 `alt + cmd + C` 去複製完整路徑，然後直接執行，程式會自己抓剪貼簿的路徑。
如果不想用複製路徑的方式，也可以直接在指令後接上路徑。

```
$ img2cloud upload -h
Description:
  從剪貼簿上傳圖片

Usage:
  upload [<path>]

Arguments:
  path                  圖片路徑

Options:
  -h, --help            Display this help message
```

2. 上傳成功後會回傳各式 URL，可以選擇要複製的連結類型，也可以不做任何動作
```
Upload completed!

=== Image info ===
Link: https://imgur.com/Z******2
Direct Link: https://i.imgur.com/Z******2.png
Type: image/png
Markdown: ![Imgur](https://i.imgur.com/Z******2.png)
HTML: <img src='https://i.imgur.com/Z******2.png' title='Imgur'/>

 複製連結類型？ [no]:
 > Link
```

## Come soon
* 歷史紀錄查詢

## License
[MIT License](https://github.com/s9801077/img2cloud-cli/blob/master/LICENSE.md)



