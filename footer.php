    <!-- Footer -->
    <?php $options = get_option( 'less_options' ); ?>
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 mt-12 transition-colors duration-200">
        <div class="container mx-auto px-4 py-8">
            <!-- Footer Bottom -->
            <div class="flex flex-col md:flex-row justify-between items-center border-t border-gray-100 dark:border-gray-700 pt-8 gap-4">
                <!-- Left: Nav -->
                <nav>
                    <?php
                    if ( has_nav_menu( 'footer' ) ) {
                        wp_nav_menu( array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400 justify-center md:justify-start',
                            'container'      => false,
                            'depth'          => 1,
                            'fallback_cb'    => false,
                        ) );
                    } else {
                        ?>
                        <ul class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-600 dark:text-gray-400 justify-center md:justify-start">
                            <li><a href="#" class="hover:text-primary transition-colors flex items-center gap-2 before:content-[''] before:w-1.5 before:h-1.5 before:bg-blue-500 before:rounded-full before:inline-block">关于我们</a></li>
                            <li><a href="#" class="hover:text-primary transition-colors flex items-center gap-2 before:content-[''] before:w-1.5 before:h-1.5 before:bg-blue-500 before:rounded-full before:inline-block">联系方式</a></li>
                            <li><a href="#" class="hover:text-primary transition-colors flex items-center gap-2 before:content-[''] before:w-1.5 before:h-1.5 before:bg-blue-500 before:rounded-full before:inline-block">隐私政策</a></li>
                        </ul>
                        <?php
                    }
                    ?>
                </nav>

                <!-- Center: Social -->
                <div class="flex items-center gap-3">
                    <?php
                    $social_order = isset($options['social_sort_order']) ? $options['social_sort_order'] : 'weibo,wechat,qq,github,x,youtube,telegram,instagram,douyin';
                    $social_items = explode(',', $social_order);
                    
                    foreach ($social_items as $item) {
                        $url = isset($options['social_' . $item]) ? $options['social_' . $item] : '';
                        
                        if (empty($url)) continue;

                        $icon_name = $item;
                        
                        switch ($item) {
                            case 'wechat': $icon_name = 'weixin'; break;
                            case 'x': $icon_name = 'x-twitter'; break;
                            case 'douyin': $icon_name = 'tiktok'; break;
                        }
                        
                        $svg = less_get_icon($icon_name, '', 'brands');
                        if (empty($svg)) continue;

                        if ($item === 'wechat' || $item === 'qq') {
                            $title = $item === 'wechat' ? '微信' : 'QQ';
                            echo '<button onclick="showQr(\'' . esc_js($url) . '\', \'' . $title . '\')" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center hover:bg-primary hover:text-white dark:hover:bg-primary transition-colors text-gray-500 dark:text-gray-400">';
                            echo $svg;
                            echo '</button>';
                        } else {
                             echo '<a href="' . esc_url($url) . '" target="_blank" rel="nofollow" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center hover:bg-primary hover:text-white dark:hover:bg-primary transition-colors text-gray-500 dark:text-gray-400">';
                             echo $svg;
                             echo '</a>';
                        }
                    }
                    ?>
                </div>

                <!-- Right: Copyright -->
                <div class="text-xs text-gray-500 text-center md:text-right">
                    <?php
                    if ( ! empty( $options['footer_copyright'] ) ) {
                        echo wp_kses_post( $options['footer_copyright'] );
                    } else {
                        echo '<p>&copy; ' . date('Y') . ' Powered By WordPress. All Rights Reserved.</p>';
                    }
                    ?>
                    <span class="ml-2">Designed by <a href="https://less-theme.com" target="_blank" class="hover:text-primary transition-colors">LessTheme</a></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Search Modal -->
    <div id="search-modal" class="fixed inset-0 bg-black/50 z-[60] hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-2xl rounded-lg shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">搜索</h3>
                <button onclick="toggleSearch()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                    <?php echo less_get_icon('xmark', 'text-xl', 'solid'); ?>
                </button>
            </div>
            <div class="p-6">
                <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <div class="relative">
                        <input type="search" class="w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:border-primary dark:focus:border-primary text-lg text-gray-900 dark:text-gray-100" placeholder="<?php echo esc_attr_x( '请输入关键词...', 'placeholder', 'less' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                        <?php echo less_get_icon('search', 'absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl', 'solid'); ?>
                    </div>
                </form>
                <?php
                $hot_words = ! empty( $options['hot_search_words'] ) ? explode( ',', $options['hot_search_words'] ) : array();
                $hot_words = array_map( 'trim', $hot_words );
                $hot_words = array_filter( $hot_words );
                
                if ( ! empty( $hot_words ) ) :
                ?>
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">热门搜索</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ( $hot_words as $word ) : ?>
                            <a href="<?php echo esc_url( home_url( '/?s=' . urlencode( $word ) ) ); ?>" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-primary hover:text-white dark:hover:bg-primary rounded-full text-sm text-gray-600 dark:text-gray-300 transition-colors"><?php echo esc_html( $word ); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qr-modal" class="fixed inset-0 bg-black/50 z-[70] hidden opacity-0 transition-opacity duration-300 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 w-full max-w-sm rounded-lg shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 relative">
            <button onclick="closeQr()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none z-10">
                <?php echo less_get_icon('xmark', 'text-xl', 'solid'); ?>
            </button>
            <div class="p-8 flex flex-col items-center">
                <h3 id="qr-title" class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-6"></h3>
                <div class="bg-white p-2 rounded-lg shadow-sm">
                    <img id="qr-image" src="" alt="QR Code" class="w-48 h-48 object-contain">
                </div>
                <p class="text-sm text-gray-500 mt-4">扫一扫上面的二维码图案，加我微信/QQ</p>
            </div>
        </div>
    </div>

    <script>
    function showQr(url, title) {
        const modal = document.getElementById('qr-modal');
        const modalContent = modal.querySelector('div');
        const qrImage = document.getElementById('qr-image');
        const qrTitle = document.getElementById('qr-title');

        qrImage.src = url;
        qrTitle.textContent = title;

        modal.classList.remove('hidden');
        // Trigger reflow
        void modal.offsetWidth;
        modal.classList.remove('opacity-0');
        modalContent.classList.remove('scale-95');
        modalContent.classList.add('scale-100');
    }

    function closeQr() {
        const modal = document.getElementById('qr-modal');
        const modalContent = modal.querySelector('div');

        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    
    // Close on click outside
    document.getElementById('qr-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQr();
        }
    });
    </script>

<?php wp_footer(); ?>
<?php 
// Custom Footer Code
if ( ! empty( $options['code_footer'] ) ) {
    echo $options['code_footer'] . "\n";
}
?>
</body>
</html>
