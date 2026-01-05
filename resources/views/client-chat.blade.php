<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8"/>
    <title>Chat UI</title>
    <style>
        *{
            border: none !important;
        }

        /* دکمه شناور */
        #chat-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background: #000;
            border-radius: 100%;
            color: #fff;
            font-size: 28px;
            border: none;
            cursor: pointer;
        }

        /* پنجره چت */
        #chat-box {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 340px;
            height: 480px;
            background: #e5ddd5;
            border-radius: 12px;
            display: none;
            flex-direction: column;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        /* هدر */
        #chat-header {
            background: #2AABEE;
            color: #fff;
            padding: 12px;
            text-align: center;
            font-weight: bold;
        }

        .msg.user {
            background: #81d8d0 !important;
        }

        /* پیام‌ها */
        #chat-body {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }

        /* حباب پیام */
        .msg {
            max-width: 75%;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 10px;
            line-height: 1.6;
            font-size: 14px;
        }

        /* پیام کاربر */
        .msg.user {
            background: #dcf8c6;
            margin-right: auto;
            border-bottom-right-radius: 0;
        }

        /* پیام بات */
        .msg.bot {
            background: #ffffff;
            margin-left: auto;
            border-bottom-left-radius: 0;
        }

        /* ورودی */
        #chat-input {
            display: flex;
            padding: 8px;
            background: #f0f0f0;
        }

        #chat-input input {
            flex: 1;
            border-radius: 20px;
            border: none;
            padding: 8px 12px;
            outline: none;
        }

        #chat-input button {
            background: #2AABEE;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        #productCode {
            margin-top: 20px;
            padding: 12px;
            background-color: #e0f7fa;
            border-radius: 8px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<!-- دکمه شناور -->
<button id="chat-btn">
    💬
</button>

<!-- پنجره چت -->
<div id="chat-box">
    <div id="chat-header">پشتیبانی آنلاین</div>
    <div id="chat-body">
        <div class="msg bot">کد محصول مورد نظر را وارد بنمایید</div>
    </div>

    <div id="chat-input">
        <input type="text" id="messageInput" placeholder="پیام..."/>
        <button onclick="sendMessage()">➤</button>
    </div>
</div>

<!-- نمایش کد محصول -->
<div id="productCode">
    <!-- کد محصول اینجا نمایش داده می‌شود -->
    کد محصول: هیچ کدی ثبت نشده است.
</div>

<script>
    // گرفتن المنت‌ها از DOM
    const chatBtn = document.getElementById("chat-btn");
    const chatBox = document.getElementById("chat-box");
    const chatBody = document.getElementById("chat-body");
    const input = document.getElementById("messageInput");
    const productCodeElement = document.getElementById("productCode");

    // دکمه چت برای باز و بسته کردن پنجره
    chatBtn.onclick = () => {
        chatBox.style.display = chatBox.style.display === "flex" ? "none" : "flex";
    };

    // تابع ارسال پیام
    function sendMessage() {
        if (input.value.trim() === "") return;

        // نمایش پیام کاربر
        const userMsg = document.createElement("div");
        userMsg.className = "msg user";
        userMsg.innerText = input.value;
        chatBody.appendChild(userMsg);

        // ذخیره پیام کاربر در متغیر mohtava
        const mohtava = input.value;

        // نمایش کد محصول در قسمت پایین
        productCodeElement.innerText = `کد محصول: ${mohtava}`;

        // نمایش پیام بات
        setTimeout(() => {
            const botMsg = document.createElement("div");
            botMsg.className = "msg bot";
            botMsg.innerText = "کد محصول ثبت شد☑️";
            chatBody.appendChild(botMsg);
            chatBody.scrollTop = chatBody.scrollHeight;
        }, 600);

        // پاک کردن ورودی
        input.value = "";
        chatBody.scrollTop = chatBody.scrollHeight;
    }
</script>

</body>
</html>
