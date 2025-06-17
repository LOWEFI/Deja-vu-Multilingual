<?php

// languages/zh.php

return [

    // ------------ CORE ------------

    "language" => "语言",
    "languages" => "语言",

    "refresh" => "刷新",
    
    "incorrect_captcha" => "验证码错误 (✜︵✜)",
    "incorrect_input" => "提交的数据无效。",

    "action" => "操作",
    "action_success" => "操作成功！✅",
    "action_error" => "执行操作时发生错误。❌",

    "update" => "更新",
    "update_success" => "更新成功！✅",
    "update_error" => "更新时发生错误。❌",

    "create" => "创建",
    "add" => "添加",
    "change" => "修改",
    "quote" => "引用",
    "send" => "发送",
    "delete" => "删除",

    "flush" => "清空",
    "kick" => "踢出",

    "user" => "用户",
    "users" => "用户",

    "date" => "日期",

    "field" => "字段",
    "value" => "值",

    "page" => "页面",
    "see" => "查看",
    "show" => "显示",
    "check" => "检查",
    "priority" => "优先级",

    "captcha_help" => "请点击半开圆圈。",

    // ------------ PAGES & CONTROLLERS ------------

    // --- NOPE ---
    "nope" => "未授权 (╥︣﹏᷅╥)",

    "nope_message" => "发生错误...",

    "nope_text" =>
        <<<TEXT
    我**超级懒**得为所有情况写定制消息，你懂的。
    
    但如果你看到这里，要么是：
    - 你在以某种方式**黑客攻击**论坛（*不好，但如果你找到漏洞记得分享！* (づ｡◕‿‿◕｡)づ）。
    - 你提交的某些**数据**是**无效的**（*奇怪*）。
    - 你使用了**黑名单**里的**词语**（*去你 X 的* ୧༼ಠ益ಠ╭∩╮༽）。

    ![miaou](https://i.giphy.com/o0vwzuFwCGAFO.webp "unauthorised")
    TEXT,

    // --- GENERAL ---
    "home" => "首页",
    "discover" => "进入论坛 →",

    "forums" => "论坛 🌐",

    "last_active_users" => "最近活跃用户：",

    "visits" => "过去 24 小时独立访问量。",

    // --- HOME ---
    "welcome_message" => "欢迎来到一个尊重隐私、毫无狗屁逻辑却飞快的论坛。🚀",

    "additional_information" => "无跟踪器、无 JavaScript、无样式表。只有你的文字。👀",

    "short_list" => "在使用本论坛之前需要接受的一些规则... 📏",

    "short_list_element_1" => "年满 18 岁。",
    "short_list_element_2" => "禁止任何与未成年人有关的内容。",
    "short_list_element_3" => "禁止垃圾信息或灌水。",

    "bottom_message" => "请不要弄脏这里。👍",

    "privacy_policy" => "隐私政策",
    "terms_of_service" => "使用条款",
    "roadmap" => "路线图",

    // --- SIGN UP ---
    "sign_up" => "注册",

    "closed_registration_information" => "注册暂时关闭。我们很快回来。ℹ️",

    "already_signed_up_question" => "已经注册？",

    "sign_up_success" => "注册成功！😊 欢迎，请登录。",

    "user_exists" => "该用户名已被占用，请使用其他名称。",

    "user_passwords_mismatch" => "两次输入的密码不一致，请重试。",

    // --- SIGN IN ---
    "sign_in" => "登录",

    "not_signed_up_question" => "还没有注册？",

    "sign_out" => "退出登录",
    "sign_out_success" => "成功退出！✅",

    "user_name" => "用户名",
    "user_name_information" => "用户名长度需为 3 - 25 个字母或数字。ℹ️",

    "user_password" => "密码",
    "user_confirm_password" => "确认密码",
    "user_password_information" => "密码长度需为 6 - 65 个字符，可包含字母、数字及常用符号，不能包含 Unicode 或空格。ℹ️",

    "incorrect_user_name_or_password" => "用户名或密码错误。❌",

    "user_not_found" => "未找到用户。⚠️",

    "banned_information" => "你已被封禁。୧༼ಠ益ಠ╭∩╮༽",

    // --- ACCOUNT ---
    "account" => "账户",

    "account_details" => "账户详情",

    "update_description" => "更新我的“关于”",
    "description_information" => "“关于”必须在 1 - 5000 个字符之间。ℹ️",

    "update_avatar" => "更新头像",
    "image_information" => "图片格式必须为 JPEG、PNG 或 GIF，且小于 2MB。ℹ️",

    "user_avatar" => "头像",

    "update_password" => "更新密码",
    "user_current_password" => "当前密码",
    "user_new_password" => "新密码",
    "user_confirm_new_password" => "确认新密码",

    "incorrect_password" => "密码错误。❌",

    // --- PROFILE ---
    "profile" => "个人资料",

    "user_banned" => "已封禁。",

    "first_seen" => "👶 首次活动：",
    "last_seen" => "❓ 最后活动：",
    "total_posts" => "✍️ 帖子数：",

    "user_description" => "关于",

    "wrote_something" => "写了些什么",

    // --- Management ---
    "management" => "管理",

    "manage_site" => "站点管理",
    "site_name" => "站点名称",
    "site_description" => "站点描述",
    "site_keywords" => "站点关键词",
    "site_information" => "站点信息横幅",
    "site_registration" => "注册状态",
    "site_chat" => "聊天室访问状态",
    "site_threads_per_page" => "每页主题数",
    "site_posts_per_page" => "每页帖子数",
    "site_posts_per_profile" => "每个个人资料的帖子数",

    "manage_stickers" => "贴纸管理",
    "stickers" => "贴纸",
    "sticker_name" => "名称",
    "sticker_location" => "位置",
    "sticker_information" => "贴纸名称长度需为 1 - 40 字符。ℹ️",

    "manage_categories" => "类别管理",
    "add_category" => "添加类别",
    "category_information" => "类别名称长度需为 1 - 40 字符，优先级必须为数字。ℹ️",

    "manage_black_list" => "黑名单管理",
    "black_list_term" => "黑名单词条",
    "black_list_information" => "黑名单词条长度需为 1 - 255 字符。ℹ️",

    "manage_roles" => "角色管理",
    "user_role" => "用户角色",
    "role_name" => "角色名称",
    "role_color" => "角色颜色",
    "see_permissions" => "查看权限",
    "role_name_information" => "角色名称长度需为 1 - 40 字符。ℹ️",
    "role_color_information" => "角色颜色长度需为 4 - 7 字符，且必须为 HEX 格式。ℹ️",

    "manage_chat_rooms" => "聊天室管理",
    "flush_chat_rooms" => "清空所有聊天室",

    "term" => "词条",

    "ban" => "封禁",
    "unban" => "解除封禁",

    "promote" => "提升权限",
    "unpromote" => "撤销提升",

    "delete_user" => "删除用户",

    "delete_user_posts" => "删除用户帖子",

    "delete_user_threads" => "删除用户主题",

    "delete_user_description" => "删除用户简介",

    "delete_user_avatar" => "删除用户头像",

    "user_manage_help" => "选择一个用户或输入其用户名。",

    // --- CATEGORIES & THREADS ---

    "next" => "下一页",
    "previous" => "上一页",

    "lock" => "锁定",
    "unlock" => "解锁",
    "pin" => "置顶",
    "unpin" => "取消置顶",

    "hide" => "隐藏",
    "unhide" => "取消隐藏",

    "author" => "作者",
    "created_at" => "创建于",
    "started_by" => "由...开始",
    "last_reply" => "最后回复：",

    "categories" => "类别",
    "category" => "类别",
    "no_category_or_thread" => "未找到任何类别或主题。⚠️",
    "no_category" => "未找到任何类别。⚠️",

    "incorrect_category" => "错误的类别。",

    "search" => "搜索",
    "search_results" => "搜索结果",
    "no_search_results" => "未找到符合搜索条件的结果。⚠️",

    "back_to_list" => "返回列表",

    "preview" => "预览",

    "permalink" => "永久链接",

    "thread" => "主题",
    "no_thread" => "未找到任何主题。⚠️",
    "thread_locked" => "主题已锁定。🔒",
    "create_thread" => "创建主题",
    "thread_title" => "标题",
    "thread_title_information" => "标题长度需为 4 - 70 字符。ℹ️",
    "thread_creation_failed" => "主题创建失败。❌",
    "thread_update_failed" => "主题更新失败。❌",
    "thread_success" => "主题已成功发布。✅",

    "post" => "帖子",
    "no_post" => "未找到任何帖子。⚠️",
    "post_hidden" => "帖子已被论坛团队隐藏。（✜︵✜）",
    "create_post" => "写帖子",
    "post_content" => "想写什么就写吧！ :)",
    "post_content_information" => "内容长度需为 4 - 40000 字符。",
    "post_content_help_text" => <<<TEXT
                                # 标题 1
                                ## 标题 2
                                ### 标题 3

                                **加粗** _斜体_ ~~删除线~~

                                - 无序列表
                                  - 子项
                                1. 有序列表
                                2. 第二项

                                [链接文本](https://example.com)  
                                ![图片](https://example.com/image.jpg)

                                `行内代码`  

                                ```
                                代码块
                                ```
                                TEXT,

    "post_creation_failed" => "帖子创建失败。❌",
    "post_update" => "你可以在这里修改内容。⬇️",
    "post_success" => "帖子发布成功。✅",

    "posts" => "帖子",
    
    "posts_list" => "所有帖子",

    "show_posts" => "显示所有帖子",

    "reply" => "回复",

    "update_time_help_text" => "注意！⚠️ 发送后你有两小时可以修改此文本。请认真校对。🧐",

    "update_time_exceeded" => "修改时间已过。🕙",

    // --- CHAT ROOMS ---

    "chat" => "聊天",
    "chat_room" => "聊天室",

    "chats" => "聊天",
    "chat_rooms" => "聊天室",

    "list_of_chat_rooms" => "聊天室列表",

    "active_chat_room" => "你已连接到聊天室：",

    "flush_chat_room" => "清空聊天室。",
    "delete_chat_room" => "删除聊天室。",

    "pause_chat_room" => "暂停滚动。",
    "unpause_chat_room" => "恢复滚动。",

    "empty_chat_room" => "聊天室为空。",

    "kicked" => "刚被踢出",

    "closed_chat_information" => "聊天室暂时关闭，给您带来不便敬请谅解。ℹ️",

    "kicked_information" => "你已被暂时踢出聊天室，请稍后再试。ℹ️",

    "level_information" => "仅对发帖用户开放聊天室，请多发帖以自动获得权限。ℹ️",
];
