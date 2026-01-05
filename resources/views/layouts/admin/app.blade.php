<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8"/>
    <title>Chat UI - 4 Columns</title>
{{--        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))--}}
{{--            @vite(['resources/css/app.css', 'resources/js/app.js'])--}}
{{--        @endif--}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        .middle {
            vertical-align: middle;
        !important;
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
            bottom: 20px;
            right: 20px;
            width: 340px;
            height: 280px;
            background: #e5ddd5;
            border-radius: 12px;
            display: flex;
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

        .back_icon {
            width: 0;
            opacity: 0;
            transition: .5s opacity;
        }

        .parent_of_back_icon:hover {
            .back_icon {
                width: auto;
                opacity: 1;
                transition: 1s opacity;
            }
        }

    </style>

    <title>{{ $title ?? 'Page Title' }}</title>
</head>

<body class="bg-gray-100 flex items-center justify-center">
{{$slot}}
</body>
</html>
