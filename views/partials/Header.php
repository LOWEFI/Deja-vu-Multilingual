<?php // views/partials/Header.php ?>

<!DOCTYPE html>

<html>

<head>

    <style>
        :root {
            --width: 800px;
            --font-main: Verdana, sans-serif;
            --font-secondary: Verdana, sans-serif;
            --font-scale: 1em;
            --background-color: #0b0b0c;
            --heading-color: #eee;
            --secondary-color: #1c1e23;
            --text-color: #ddd;
            --link-color: #00FF41;
            --visited-color: #008F11;
            --code-background-color: #000;
            --code-color: #ddd;
            --blockquote-color: #ccc;
        }

        body {
            font-family: var(--font-secondary);
            font-size: var(--font-scale);
            margin: auto;
            padding: 20px;
            max-width: var(--width);
            text-align: left;
            background-color: var(--background-color);
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.5;
            color: var(--text-color);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: var(--font-main);
            color: var(--heading-color);
        }

        a {
            color: var(--link-color);
            cursor: pointer;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        nav a {
            margin-right: 8px;
        }

        strong,
        b {
            color: var(--heading-color);
        }

        button {
            margin: 0;
            cursor: pointer;
        }

        main {
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
        }

        hr {
            border: 0;
            border-top: 1px dashed;
        }

        img {
            max-width: 100%;
        }

        code {
            font-family: monospace;
            padding: 2px;
            background-color: var(--code-background-color);
            color: var(--code-color);
            border-radius: 3px;
            display: block;
            white-space: pre;
            overflow-x: auto;
        }

        blockquote,
        blockquote blockquote {
            border-left: 1px solid #999;
            color: var(--code-color);
            padding-left: 20px;
            font-style: italic;
            font-size: 10px;
        }

        footer {
            padding: 25px 0;
            text-align: center;
        }

        .title:hover {
            text-decoration: none;
        }

        .title h1 {
            font-size: 1.5em;
        }

        .inline {
            width: auto !important;
        }

        .highlight,
        .code {
            padding: 1px 15px;
            background-color: var(--code-background-color);
            color: var(--code-color);
            border-radius: 3px;
            margin-block-start: 1em;
            margin-block-end: 1em;
            overflow-x: auto;
        }

        ul {
            list-style: disc;
            padding-left: 15px;
            padding-right: 10px;
        }

        ul.blog-posts {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        ul.blog-posts li {
            display: flex;
            position: relative;
            padding-left: 20px;
            margin-bottom: 15px;
        }

        ul.blog-posts li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 2px;
            height: 100%;
            background-color: #000;
        }

        ul.blog-posts li span {
            flex: 0 0 130px;
        }

        ul.blog-posts li a:visited {
            color: var(--visited-color);
        }

        .post-profile-container {}

        .post-profile {
            display: inline-block;
            vertical-align: top;
        }

        .top-info {
            line-height: 1.2;
            word-break: break-all;
        }

        .bottom-info {
            line-height: 1.2;
        }

        .post-container {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--code-background-color);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(255, 255, 255, 0.1);
            transition: background-color 0.3s ease;

        }

        .post-container img {
            max-width: 30%;
        }

        .post-container:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .post-container p {
            margin-top: 10px;
            line-height: 1.6;
        }

        .post-action-container {
            text-align: right;
        }

        .emoji {
            font-weight: 400;
            font-family: "Lucida", monospace;
            white-space: nowrap;
            display: inline-block;
        }

        .flex::before {
            content: "ᕦʕ •ᴥ•ʔᕤ";
            animation: blink 10s infinite;
        }

        .flex:hover::before {
            content: "ᕙʕ ಠᴥಠʔᕗ";
            animation: none;
        }

        .upgrade:hover .flex::before {
            content: "ᕙʕ ಠᴥಠʔᕗ";
            animation: none;
            text-decoration: underline;
        }

        @keyframes blink {

            0%,
            96% {
                content: "ᕦʕ •ᴥ•ʔᕤ";
            }

            97%,
            100% {
                content: "ᕦʕ -ᴥ-ʔᕤ";
            }
        }

        a.account:hover {
            text-decoration: none;
        }

        a.account::before {
            content: "ʕ-ᴥ-ʔ";
        }

        .account:hover::before {
            content: "ʕ•ᴥ•ʔ";
        }

        header nav {
            display: flex;
            flex-flow: row wrap;
            justify-content: space-between;
        }

        header nav a:last-child {
            margin-right: 0;
        }

        label {
            font-weight: bold;
        }

        time {
            font-family: monospace;
            font-size: 15px;
        }

        form table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        form table thead {
            display: none;
        }

        form table tbody tr {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            justify-content: flex-start;
        }

        form table tbody tr td {
            box-sizing: border-box;
        }

        form table tbody tr td input button {
            width: 100%;
            box-sizing: border-box;
            margin-top: 10px;
            height: 2rem;
        }

        textarea,
        input:not([type="submit"]),
        .editable {
            background-color: var(--secondary-color);
            border: none;
            line-height: 1.3;
            outline: none;
            color: inherit;
            padding: 10px;
            font-size: 18px;
        }

        input {
            height: 0.9rem;
        }

        input[type="file"] {
            height: auto;
            line-height: 1.8rem;
            padding: 0.4rem;
        }

        textarea {
            height: 10rem;
        }

        .full-width textarea,
        .full-width input:not([type="submit"], [type="checkbox"], [type="radio"]) {
            width: calc(100% - 20px);
        }

        textarea:required,
        input:required:not([type="submit"]) {
            border-left: 1px solid #f99f9f;
        }

        input[type='checkbox'],
        input[type='radio'] {
            height: 15px;
            margin-right: 10px;
        }

        button {
            margin: 5px 0;
        }

        button.floating {
            position: fixed;
            bottom: 20px;
            right: 25px;
        }

        button#toggle-full-screen {
            float: right;
        }

        .sticky-controls {
            position: sticky;
            top: 0;
            background-color: var(--background-color);
            line-height: 0;
            z-index: 10;
        }

        .helptext {
            display: flex;
            justify-content: space-between;
            color: #777;
            font-size: small;
            line-height: 2.4;
        }

        .helptext.sticky {
            position: sticky;
            bottom: 0;
            background-color: var(--background-color);
            padding: 3px 0;
        }

        .popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            max-width: 800px;
            width: 90%;
            max-height: 90%;
            overflow: auto;
        }

        .successes {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }


        .errors {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }


        .errors ul,
        .successes ul {
            list-style-type: none;
            padding-left: 0;
            margin: 0;
        }

        .errors li,
        .successes li {
            margin-bottom: 5px;
        }

        .errorlist {
            color: #eba613;
            font-size: small;
        }

        #date-range {
            display: none;
        }

        #date-range:target {
            display: block;
        }

        .notification {
            width: 100%;
            text-align: left;
            padding: 5px 0;
            margin-bottom: -15px;
        }

        .notification a {
            margin: 0;
        }


        ul.post-list {
            list-style-type: none;
            padding: unset;
        }

        ul.post-list li {
            display: flex;
            align-items: baseline;
            padding: 10px 0;
            border-bottom: 1px solid #eceff4;
        }

        ul.post-list li span {
            flex: 0 0 130px;
        }

        ul.post-list li span.number {
            flex: 0 0 50px;
        }

        ul.post-list li a {
            flex: max-content;
        }

        ul.post-list li small {
            text-align: right;
            flex: 0 0 115px;
        }

        /* media center */
        .media-container {
            display: flex;
            flex-flow: row wrap;
            gap: 5px;
        }

        .media-item {
            text-align: center;
            background-color: #eceff4;
            position: relative;
        }

        .media-checkbox {
            position: absolute;
            top: 5px;
            right: 5px;
            margin: 0 !important;
            height: unset !important;
        }

        /* discovery feed */
        ul.discover-posts {
            list-style-type: none;
            padding: unset;
        }

        ul.discover-posts li {
            display: flex;
            line-height: 1.2;
            position: relative;
        }

        ul.discover-posts li span {
            flex: 0 0 40px;
        }

        ul.discover-posts li a:visited {
            color: #8b6fcb;
        }

        ul.discover-posts li div {
            padding-bottom: 8px;
        }

        ul.discover-posts li small span,
        ul.discover-posts li small span a {
            color: #777 !important;
        }

        @media (prefers-color-scheme: dark) {
            .helptext {
                color: #aaa;
            }
        }

        .profile-container {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 20px;
        }

        .profile-avatar {
            margin-bottom: 10px;
            max-width: 150px;
        }

        .profile-text {}

        @media (max-width: 600px) {
            .profile-container {
                flex-direction: column;
                gap: 0;
            }
        }

        .stickers-container {
            display: flex;
            flex-wrap: wrap;
            box-sizing: border-box;
            gap: 10px;
            max-height: 180px;
            overflow-y: auto;
            margin-bottom: 10px;
        }

        .sticker {
            position: relative;
            flex: 0 0 auto;
            border: 0.5px solid var(--link-color);
            border-radius: 8px;
            padding: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: var(--secondary-color);
        }

        .sticker img {
            width: 50px;
            height: auto;
            display: block;
        }

        .sticker div {
            margin-top: 2px;
            font-size: 10px;
            text-align: center;
        }

        .listing-container {
            display: flex;
            flex-wrap: wrap;
            box-sizing: border-box;
            gap: 10px;
            max-height: 180px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
    </style>

    <meta charset="UTF-8">
    <title><?= sanitise_output($data['site_data']['site_title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitise_output($data['site_data']['site_description']); ?>">
    <meta name="keywords" content="<?= sanitise_output($data['site_data']['site_keywords']); ?>">
    <!-- <link rel="preload" href="/assets/css/style.css" as="style"> -->
    <!-- <link rel="stylesheet" href="/assets/css/style.css"> -->
    <link rel="icon" href="/assets/images/favicon.png" type="image/png">
</head>

<body>

    <div id="top"></div>

    <header>
        <a class="title" href="/forum">
            <h1>
                <span class="emoji">◕_◕&nbsp;</span>
                <?= sanitise_output($data['site_data']['site_name']); ?>
            </h1>
        </a>
    </header>

    <div class="header-bis">

        <h2 style="font-size: 1.34em"><?= sanitise_output($data['site_data']['site_description']); ?></h2>

        <nav>
            <a href="/"><?= Language::get('home'); ?></a>

            <?php if (!$data['user_authenticated']): ?>
                <a href="/signup"><?= Language::get('sign_up'); ?></a>
                <a href="/signin"><?= Language::get('sign_in'); ?></a>
            <?php else: ?>
                <a
                    href="/account?action=signout&csrf_token=<?= urlencode($data['csrf_token']); ?>"><?= Language::get('sign_out'); ?></a>
                <a href="/account"><?= Language::get('account'); ?></a>
                <a href="/chat"><?= Language::get('chat'); ?> 🌐</a>
                <?php if ($data['role_authorization']['role_management']): ?>
                    <a href="/management"><?= Language::get('management'); ?></a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!$data['user_authenticated']): ?>
                <a href="/forums/"><?= Language::get('forums'); ?></a>
                <a href="/forums/"><?= Language::get('discover'); ?></a>
            <?php else: ?>
                <a href="/forums/"><?= Language::get('forums'); ?></a>
                <a href="/forum/"><?= Language::get('discover'); ?></a>
            <?php endif; ?>

        </nav>

        <h4>╰( ͡° ͜ʖ ͡° )つ──☆*:・ﾟ : <?= sanitise_output($data['site_data']['site_information']); ?></h4>

    </div>

    <main>

        <?= display_messages(Flash::get('errors') ?? [], Flash::get('successes') ?? []) ?>