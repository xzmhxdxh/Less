<?php
/**
 * Less Theme Options
 */

// Register Settings
function less_register_settings() {
    register_setting( 'less_options_group', 'less_options' );
}
add_action( 'admin_init', 'less_register_settings' );

// Add Menu Page
function less_add_admin_menu() {
    add_menu_page(
        'Less 主题设置',
        'Less 主题设置',
        'manage_options',
        'less_options',
        'less_options_page_html',
        'dashicons-welcome-widgets-menus',
        60
    );
}
add_action( 'admin_menu', 'less_add_admin_menu' );

// Enqueue Media Scripts
function less_admin_scripts( $hook ) {
    if ( 'toplevel_page_less_options' !== $hook ) {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script( 'jquery-ui-sortable' );
    wp_enqueue_script( 'less-admin-script', get_template_directory_uri() . '/assets/js/admin.js', array( 'jquery', 'jquery-ui-sortable' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'less_admin_scripts' );

// Options Page HTML
function less_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $defaults = array(
        'color_mode'             => 'light',
        'show_author'            => 1,
        'show_views'             => 1,
        'enable_comments'        => 0,
        'show_comments'          => 1,
        'show_likes'             => 1,
        'seo_tags_as_keywords'   => 1,
        'seo_auto_description'   => 1,
        'show_hero'              => 1,
        'hero_interval'          => 5,
        'posts_per_page_home'    => 10,
        'posts_per_page_archive' => 10,
    );
    $options = get_option( 'less_options', $defaults );
    $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=less_options&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">常规设置</a>
            <a href="?page=less_options&tab=hero" class="nav-tab <?php echo $active_tab == 'hero' ? 'nav-tab-active' : ''; ?>">幻灯片设置</a>
            <a href="?page=less_options&tab=search" class="nav-tab <?php echo $active_tab == 'search' ? 'nav-tab-active' : ''; ?>">搜索设置</a>
            <a href="?page=less_options&tab=seo" class="nav-tab <?php echo $active_tab == 'seo' ? 'nav-tab-active' : ''; ?>">SEO 设置</a>
            <a href="?page=less_options&tab=footer" class="nav-tab <?php echo $active_tab == 'footer' ? 'nav-tab-active' : ''; ?>">底部设置</a>
            <a href="?page=less_options&tab=display" class="nav-tab <?php echo $active_tab == 'display' ? 'nav-tab-active' : ''; ?>">显示设置</a>
            <a href="?page=less_options&tab=code" class="nav-tab <?php echo $active_tab == 'code' ? 'nav-tab-active' : ''; ?>">自定义代码</a>
            <a href="?page=less_options&tab=about" class="nav-tab <?php echo $active_tab == 'about' ? 'nav-tab-active' : ''; ?>">关于主题</a>
        </h2>

        <form action="options.php" method="post">
            <?php
            settings_fields( 'less_options_group' );
            
            // General Tab
            ?>
            <div style="<?php echo $active_tab == 'general' ? '' : 'display:none;'; ?>">
                <h3>常规设置</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Logo (浅色模式)</th>
                        <td>
                            <input type="text" name="less_options[logo_url_light]" id="logo_url_light" value="<?php echo isset($options['logo_url_light']) ? esc_attr($options['logo_url_light']) : (isset($options['logo_url']) ? esc_attr($options['logo_url']) : ''); ?>" class="regular-text">
                            <input type="button" class="button button-secondary js-upload-image" data-target="#logo_url_light" value="上传 Logo">
                            <p class="description">上传或选择浅色模式下的 Logo 图片。如果留空，将使用站点标题。</p>
                            <?php 
                            $logo_light_preview = isset($options['logo_url_light']) ? $options['logo_url_light'] : (isset($options['logo_url']) ? $options['logo_url'] : '');
                            if ( ! empty( $logo_light_preview ) ) : 
                            ?>
                                <div class="less-image-preview" style="margin-top:10px;"><img src="<?php echo esc_url($logo_light_preview); ?>" style="max-height: 50px;"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Logo (深色模式)</th>
                        <td>
                            <input type="text" name="less_options[logo_url_dark]" id="logo_url_dark" value="<?php echo isset($options['logo_url_dark']) ? esc_attr($options['logo_url_dark']) : ''; ?>" class="regular-text">
                            <input type="button" class="button button-secondary js-upload-image" data-target="#logo_url_dark" value="上传 Logo">
                            <p class="description">上传或选择深色模式下的 Logo 图片。</p>
                            <?php if ( ! empty( $options['logo_url_dark'] ) ) : ?>
                                <div class="less-image-preview" style="margin-top:10px; background: #333; padding: 5px; display: inline-block;"><img src="<?php echo esc_url($options['logo_url_dark']); ?>" style="max-height: 50px;"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Favicon</th>
                        <td>
                            <input type="text" name="less_options[favicon_url]" id="favicon_url" value="<?php echo isset($options['favicon_url']) ? esc_attr($options['favicon_url']) : ''; ?>" class="regular-text">
                            <input type="button" class="button button-secondary js-upload-image" data-target="#favicon_url" value="上传 Favicon">
                            <p class="description">上传或选择网站图标（Favicon）。建议尺寸 32x32 或更大。</p>
                            <?php if ( ! empty( $options['favicon_url'] ) ) : ?>
                                <div class="less-image-preview" style="margin-top:10px;"><img src="<?php echo esc_url($options['favicon_url']); ?>" style="max-height: 32px;"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?php

            // Hero Tab
            ?>
            <div style="<?php echo $active_tab == 'hero' ? '' : 'display:none;'; ?>">
                <h3>幻灯片设置</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">显示 Hero 区域</th>
                        <td>
                            <input type="checkbox" name="less_options[show_hero]" value="1" <?php checked( isset($options['show_hero']) && $options['show_hero'] == 1 ); ?>>
                            <label for="less_options[show_hero]">在首页显示 Hero 区域（幻灯片和右侧卡片）</label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">幻灯片自动切换时间 (秒)</th>
                        <td>
                            <input type="number" name="less_options[hero_interval]" value="<?php echo isset($options['hero_interval']) ? intval($options['hero_interval']) : 5; ?>" min="1" step="1" class="small-text">
                            <p class="description">设置幻灯片自动轮播的时间间隔，默认为 5 秒。</p>
                        </td>
                    </tr>
                </table>
                <p class="description">配置首页 Hero 区的 3 张幻灯片。</p>
                <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                    <hr>
                    <h4>幻灯片 <?php echo $i; ?></h4>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">图片</th>
                            <td>
                                <input type="text" name="less_options[hero_slide_<?php echo $i; ?>_img]" id="hero_slide_<?php echo $i; ?>_img" value="<?php echo isset($options['hero_slide_'.$i.'_img']) ? esc_attr($options['hero_slide_'.$i.'_img']) : ''; ?>" class="regular-text">
                                <input type="button" class="button button-secondary js-upload-image" data-target="#hero_slide_<?php echo $i; ?>_img" value="上传图片">
                                <?php if ( ! empty( $options['hero_slide_'.$i.'_img'] ) ) : ?>
                                    <div class="less-image-preview" style="margin-top:10px;"><img src="<?php echo esc_url($options['hero_slide_'.$i.'_img']); ?>" style="max-height: 100px;"></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">标题</th>
                            <td>
                                <input type="text" name="less_options[hero_slide_<?php echo $i; ?>_title]" value="<?php echo isset($options['hero_slide_'.$i.'_title']) ? esc_attr($options['hero_slide_'.$i.'_title']) : ''; ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">链接地址</th>
                            <td>
                                <input type="text" name="less_options[hero_slide_<?php echo $i; ?>_link]" value="<?php echo isset($options['hero_slide_'.$i.'_link']) ? esc_attr($options['hero_slide_'.$i.'_link']) : ''; ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">在新窗口打开</th>
                            <td>
                                <input type="checkbox" name="less_options[hero_slide_<?php echo $i; ?>_open_new]" value="1" <?php checked( ! isset($options['hero_slide_'.$i.'_open_new']) || $options['hero_slide_'.$i.'_open_new'] == 1 ); ?>>
                                <label for="less_options[hero_slide_<?php echo $i; ?>_open_new]">开启</label>
                            </td>
                        </tr>
                    </table>
                <?php endfor; ?>
                
                <hr>
                <h4>右侧卡片设置</h4>
                <p class="description">配置首页 Hero 区右侧的 2 张卡片。</p>
                <?php for ( $i = 1; $i <= 2; $i++ ) : ?>
                    <h5>卡片 <?php echo $i; ?></h5>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">图片</th>
                            <td>
                                <input type="text" name="less_options[hero_card_<?php echo $i; ?>_img]" id="hero_card_<?php echo $i; ?>_img" value="<?php echo isset($options['hero_card_'.$i.'_img']) ? esc_attr($options['hero_card_'.$i.'_img']) : ''; ?>" class="regular-text">
                                <input type="button" class="button button-secondary js-upload-image" data-target="#hero_card_<?php echo $i; ?>_img" value="上传图片">
                                <?php if ( ! empty( $options['hero_card_'.$i.'_img'] ) ) : ?>
                                    <div class="less-image-preview" style="margin-top:10px;"><img src="<?php echo esc_url($options['hero_card_'.$i.'_img']); ?>" style="max-height: 100px;"></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">标题</th>
                            <td>
                                <input type="text" name="less_options[hero_card_<?php echo $i; ?>_title]" value="<?php echo isset($options['hero_card_'.$i.'_title']) ? esc_attr($options['hero_card_'.$i.'_title']) : ''; ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">链接地址</th>
                            <td>
                                <input type="text" name="less_options[hero_card_<?php echo $i; ?>_link]" value="<?php echo isset($options['hero_card_'.$i.'_link']) ? esc_attr($options['hero_card_'.$i.'_link']) : ''; ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">在新窗口打开</th>
                            <td>
                                <input type="checkbox" name="less_options[hero_card_<?php echo $i; ?>_open_new]" value="1" <?php checked( ! isset($options['hero_card_'.$i.'_open_new']) || $options['hero_card_'.$i.'_open_new'] == 1 ); ?>>
                                <label for="less_options[hero_card_<?php echo $i; ?>_open_new]">开启</label>
                            </td>
                        </tr>
                    </table>
                <?php endfor; ?>
            </div>
            <?php

            // Search Tab
            ?>
            <div style="<?php echo $active_tab == 'search' ? '' : 'display:none;'; ?>">
                <h3>搜索设置</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">热门搜索关键词</th>
                        <td>
                            <textarea name="less_options[hot_search_words]" rows="5" cols="50" class="large-text code"><?php echo isset($options['hot_search_words']) ? esc_textarea($options['hot_search_words']) : ''; ?></textarea>
                            <p class="description">输入关键词，用逗号分隔（例如：人工智能, 设计, WordPress）。</p>
                        </td>
                    </tr>
                </table>
            </div>
            <?php

            // SEO Tab
            ?>
            <div style="<?php echo $active_tab == 'seo' ? '' : 'display:none;'; ?>">
                <h3>SEO 设置</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">文章关键词设置</th>
                        <td>
                            <input type="checkbox" name="less_options[seo_tags_as_keywords]" value="1" <?php checked( isset($options['seo_tags_as_keywords']) && $options['seo_tags_as_keywords'] == 1 ); ?>>
                            <label for="less_options[seo_tags_as_keywords]">自动将文章标签作为关键词</label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">文章描述设置</th>
                        <td>
                            <input type="checkbox" name="less_options[seo_auto_description]" value="1" <?php checked( isset($options['seo_auto_description']) && $options['seo_auto_description'] == 1 ); ?>>
                            <label for="less_options[seo_auto_description]">自动获取文章摘要或截断内容作为描述</label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">首页标题</th>
                        <td>
                            <input type="text" name="less_options[seo_home_title]" value="<?php echo isset($options['seo_home_title']) ? esc_attr($options['seo_home_title']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">首页描述</th>
                        <td>
                            <textarea name="less_options[seo_home_desc]" rows="3" cols="50" class="large-text"><?php echo isset($options['seo_home_desc']) ? esc_textarea($options['seo_home_desc']) : ''; ?></textarea>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">首页关键词</th>
                        <td>
                            <input type="text" name="less_options[seo_home_keywords]" value="<?php echo isset($options['seo_home_keywords']) ? esc_attr($options['seo_home_keywords']) : ''; ?>" class="regular-text">
                            <p class="description">用逗号分隔。</p>
                        </td>
                    </tr>
                </table>
            </div>
            <?php

            // Footer Tab
            ?>
            <div style="<?php echo $active_tab == 'footer' ? '' : 'display:none;'; ?>">
                <h3>底部设置</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">版权信息</th>
                        <td>
                            <textarea name="less_options[footer_copyright]" rows="3" cols="50" class="large-text"><?php echo isset($options['footer_copyright']) ? esc_textarea($options['footer_copyright']) : ''; ?></textarea>
                            <p class="description">支持 HTML 代码。</p>
                        </td>
                    </tr>
                </table>
                
                <hr>
                <h3>社交图标排序</h3>
                <p class="description">拖动图标进行排序。</p>
                <input type="hidden" name="less_options[social_sort_order]" id="social_sort_order" value="<?php echo isset($options['social_sort_order']) ? esc_attr($options['social_sort_order']) : ''; ?>">
                <ul id="social-sort-list" style="margin-bottom: 20px; max-width: 600px;">
                    <?php
                    $default_order = 'weibo,wechat,qq,github,x,youtube,telegram,instagram,xiaohongshu,douyin,social_custom_1,social_custom_2,social_custom_3';
                    $saved_order = isset($options['social_sort_order']) && !empty($options['social_sort_order']) ? $options['social_sort_order'] : $default_order;
                    $order_array = explode(',', $saved_order);
                    
                    $labels = array(
                        'weibo' => '微博',
                        'wechat' => '微信',
                        'qq' => 'QQ',
                        'github' => 'Github',
                        'x' => 'X (Twitter)',
                        'youtube' => 'YouTube',
                        'telegram' => 'Telegram',
                        'instagram' => 'Instagram',
                        'xiaohongshu' => '小红书',
                        'douyin' => '抖音',
                        'social_custom_1' => '自定义图标 1',
                        'social_custom_2' => '自定义图标 2',
                        'social_custom_3' => '自定义图标 3',
                    );

                    foreach ($order_array as $key) {
                        if (isset($labels[$key])) {
                            echo '<li data-key="' . esc_attr($key) . '" style="background: #fff; border: 1px solid #ccd0d4; padding: 10px; margin-bottom: 5px; cursor: move; display: flex; align-items: center;"><span class="dashicons dashicons-menu" style="margin-right: 10px; color: #a0a5aa;"></span>' . esc_html($labels[$key]) . '</li>';
                        }
                    }
                    ?>
                </ul>

                <h3>社交图标设置</h3>
                <p class="description">设置底部显示的社交媒体图标链接。留空则不显示。</p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">微博链接</th>
                        <td>
                            <input type="text" name="less_options[social_weibo]" value="<?php echo isset($options['social_weibo']) ? esc_attr($options['social_weibo']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">微信二维码链接</th>
                        <td>
                            <input type="text" name="less_options[social_wechat]" id="social_wechat" value="<?php echo isset($options['social_wechat']) ? esc_attr($options['social_wechat']) : ''; ?>" class="regular-text">
                            <input type="button" class="button button-secondary js-upload-image" data-target="#social_wechat" value="上传图片">
                            <p class="description">上传或输入微信二维码图片的 URL 地址。</p>
                            <?php if ( ! empty( $options['social_wechat'] ) ) : ?>
                                <div style="margin-top:10px;"><img src="<?php echo esc_url($options['social_wechat']); ?>" style="max-height: 100px;"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">QQ 二维码链接</th>
                        <td>
                            <input type="text" name="less_options[social_qq]" id="social_qq" value="<?php echo isset($options['social_qq']) ? esc_attr($options['social_qq']) : ''; ?>" class="regular-text">
                            <input type="button" class="button button-secondary js-upload-image" data-target="#social_qq" value="上传图片">
                            <p class="description">上传或输入 QQ 二维码图片的 URL 地址。</p>
                            <?php if ( ! empty( $options['social_qq'] ) ) : ?>
                                <div style="margin-top:10px;"><img src="<?php echo esc_url($options['social_qq']); ?>" style="max-height: 100px;"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Github 链接</th>
                        <td>
                            <input type="text" name="less_options[social_github]" value="<?php echo isset($options['social_github']) ? esc_attr($options['social_github']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">X (Twitter) 链接</th>
                        <td>
                            <input type="text" name="less_options[social_x]" value="<?php echo isset($options['social_x']) ? esc_attr($options['social_x']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">YouTube 链接</th>
                        <td>
                            <input type="text" name="less_options[social_youtube]" value="<?php echo isset($options['social_youtube']) ? esc_attr($options['social_youtube']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Telegram 链接</th>
                        <td>
                            <input type="text" name="less_options[social_telegram]" value="<?php echo isset($options['social_telegram']) ? esc_attr($options['social_telegram']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Instagram 链接</th>
                        <td>
                            <input type="text" name="less_options[social_instagram]" value="<?php echo isset($options['social_instagram']) ? esc_attr($options['social_instagram']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">小红书链接</th>
                        <td>
                            <input type="text" name="less_options[social_xiaohongshu]" value="<?php echo isset($options['social_xiaohongshu']) ? esc_attr($options['social_xiaohongshu']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">抖音链接</th>
                        <td>
                            <input type="text" name="less_options[social_douyin]" value="<?php echo isset($options['social_douyin']) ? esc_attr($options['social_douyin']) : ''; ?>" class="regular-text">
                        </td>
                    </tr>
                </table>

                <hr>
                <h4>自定义图标</h4>
                <p class="description">添加额外的自定义图标。图标类名请参考 <a href="https://fontawesome.com/search?o=r&m=free" target="_blank">FontAwesome</a> (例如: fas fa-envelope)。</p>
                <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                    <h5>自定义图标 <?php echo $i; ?></h5>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">图标类名</th>
                            <td>
                                <input type="text" name="less_options[social_custom_<?php echo $i; ?>_icon]" value="<?php echo isset($options['social_custom_'.$i.'_icon']) ? esc_attr($options['social_custom_'.$i.'_icon']) : ''; ?>" class="regular-text" placeholder="例如: fas fa-envelope">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">链接地址</th>
                            <td>
                                <input type="text" name="less_options[social_custom_<?php echo $i; ?>_link]" value="<?php echo isset($options['social_custom_'.$i.'_link']) ? esc_attr($options['social_custom_'.$i.'_link']) : ''; ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">标题 (Tooltip)</th>
                            <td>
                                <input type="text" name="less_options[social_custom_<?php echo $i; ?>_title]" value="<?php echo isset($options['social_custom_'.$i.'_title']) ? esc_attr($options['social_custom_'.$i.'_title']) : ''; ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                <?php endfor; ?>
            </div>
            <?php

            // Display Tab
            ?>
            <div style="<?php echo $active_tab == 'display' ? '' : 'display:none;'; ?>">
                <h3>显示设置</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">颜色模式</th>
                        <td>
                            <select name="less_options[color_mode]">
                                <option value="auto" <?php selected( isset($options['color_mode']) ? $options['color_mode'] : 'light', 'auto' ); ?>>根据设备状态自动匹配</option>
                                <option value="light" <?php selected( isset($options['color_mode']) ? $options['color_mode'] : 'light', 'light' ); ?>>浅色模式</option>
                                <option value="dark" <?php selected( isset($options['color_mode']) ? $options['color_mode'] : 'light', 'dark' ); ?>>深色模式</option>
                            </select>
                            <p class="description">选择网站的默认颜色模式。</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">首页文章显示数量</th>
                        <td>
                            <input type="number" name="less_options[posts_per_page_home]" value="<?php echo isset($options['posts_per_page_home']) ? esc_attr($options['posts_per_page_home']) : '10'; ?>" class="small-text" min="1" step="1">
                            <p class="description">首页每页显示的文章数量（默认为 10）。</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">列表页文章显示数量</th>
                        <td>
                            <input type="number" name="less_options[posts_per_page_archive]" value="<?php echo isset($options['posts_per_page_archive']) ? esc_attr($options['posts_per_page_archive']) : '10'; ?>" class="small-text" min="1" step="1">
                            <p class="description">分类、标签、搜索等列表页每页显示的文章数量（默认为 10）。</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">显示作者名</th>
                        <td>
                            <input type="checkbox" name="less_options[show_author]" value="1" <?php checked( isset($options['show_author']) && $options['show_author'] == 1 ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">显示文章浏览数</th>
                        <td>
                            <input type="checkbox" name="less_options[show_views]" value="1" <?php checked( isset($options['show_views']) && $options['show_views'] == 1 ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">显示评论数</th>
                        <td>
                            <input type="checkbox" name="less_options[show_comments]" value="1" <?php checked( isset($options['show_comments']) && $options['show_comments'] == 1 ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">显示点赞数</th>
                        <td>
                            <input type="checkbox" name="less_options[show_likes]" value="1" <?php checked( isset($options['show_likes']) && $options['show_likes'] == 1 ); ?>>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">是否开启评论功能</th>
                        <td>
                            <input type="checkbox" name="less_options[enable_comments]" value="1" <?php checked( isset($options['enable_comments']) && $options['enable_comments'] == 1 ); ?>>
                            <label for="less_options[enable_comments]">开启全站评论功能（不勾选则不显示评论区）</label>
                        </td>
                    </tr>
                </table>
            </div>

            <?php
            // Code Tab
            ?>
            <div style="<?php echo $active_tab == 'code' ? '' : 'display:none;'; ?>">
                <h3>自定义代码</h3>
                <p class="description">在此处添加自定义代码，如 Google Analytics 统计代码、自定义 CSS 样式等。</p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">页头代码 (Header Code)</th>
                        <td>
                            <textarea name="less_options[code_head]" rows="8" cols="50" class="large-text code" placeholder="<script>...</script>"><?php echo isset($options['code_head']) ? esc_textarea($options['code_head']) : ''; ?></textarea>
                            <p class="description">位于 <code>&lt;/head&gt;</code> 标签之前。适合放置统计代码、Meta 标签等。</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">页脚代码 (Footer Code)</th>
                        <td>
                            <textarea name="less_options[code_footer]" rows="8" cols="50" class="large-text code" placeholder="<script>...</script>"><?php echo isset($options['code_footer']) ? esc_textarea($options['code_footer']) : ''; ?></textarea>
                            <p class="description">位于 <code>&lt;/body&gt;</code> 标签之前。适合放置统计代码、JS 脚本等。</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">自定义 CSS (Custom CSS)</th>
                        <td>
                            <textarea name="less_options[code_css]" rows="10" cols="50" class="large-text code" placeholder="body { ... }"><?php echo isset($options['code_css']) ? esc_textarea($options['code_css']) : ''; ?></textarea>
                            <p class="description">自定义 CSS 样式，无需包含 <code>&lt;style&gt;</code> 标签。</p>
                        </td>
                    </tr>
                </table>
            </div>

            <?php
            // About Tab
            ?>
            <div style="<?php echo $active_tab == 'about' ? '' : 'display:none;'; ?>">
                <h3>关于主题</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">主题名称</th>
                        <td>LessTheme</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">版本号</th>
                        <td>1.0.0 (2025.12.12)</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">主题官网</th>
                        <td><a href="https://less-theme.com" target="_blank">https://less-theme.com</a></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Github 存档</th>
                        <td><a href="https://less-theme.com" target="_blank">https://less-theme.com</a></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">交流 QQ 群</th>
                        <td>466268334</td>
                    </tr>
                </table>
            </div>

            <?php submit_button( '保存更改' ); ?>
        </form>
    </div>
    <?php
}
