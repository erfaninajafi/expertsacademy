<footer>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <?php
                $resultMenu = Query("select * from tbl_menufooter where tbl_menufooter_parentid='0' and tbl_menufooter_lng='$lng' order by tbl_menufooter_order ASC");
                while ($readMenu = mysqli_fetch_array($resultMenu)) {
                    $parentId = $readMenu['tbl_menufooter_code'];
                    echo '<ul><li>' . $readMenu['tbl_menufooter_title'] . '</li>';
                    $resultMenuChild = Query("select * from tbl_menufooter where tbl_menufooter_parentid='$parentId' and tbl_menufooter_lng='$lng' order by tbl_menufooter_order ASC");
                    if (mysqli_num_rows($resultMenuChild) > 0) {
                        while ($readMenuChild = mysqli_fetch_array($resultMenuChild)) {
                            echo '<li><a href="' . $readMenuChild['tbl_menufooter_link'] . '" >' . $readMenuChild['tbl_menufooter_title'] . '</a></li>';
                        }
                    }

                    echo '</ul>';
                }
                ?>
            </div>
            <div class="col-12 col-lg-4">
                <div class="footer-address">
                    <p><?= $lang[42] ?></p>
                    <p><?= $lang[43] ?></p>
                </div>
                <div class="footer-tel">
                    <div><span class="icon-phone21"></span><a href="#"><span><?= $lang[46] ?></span></a></div>
                    <div><span class="icon-whatsapp1"></span><a href="#"><span><?= $lang[47] ?></span></a></div>
                    <div><span class="icon-mail"></span><a href="#"><span><?= $lang[48] ?></span></a></div>
                </div>
                <div class="footer-social">
                    <?= (!empty($insta_setting) ? '<a href="' . $insta_setting . '" target="_blank"><span class="icon-instagram"></span></a>' : '') ?>
                    <?= (!empty($whatsapp_setting) ? '<a href="' . $whatsapp_setting . '" target="_blank"><span class="icon-whatsapp1"></span></a>' : '') ?>
                    <?= (!empty($linkedin_setting) ? '<a href="' . $linkedin_setting . '" target="_blank"><span class="icon-linkedin2"></span></a>' : '') ?>
                    <?= (!empty($twitter_setting) ? '<a href="' . $twitter_setting . '" target="_blank"><span class="icon-twitter"></span></a>' : '') ?>
                    <?= (!empty($facebook_setting) ? '<a href="' . $facebook_setting . '" target="_blank"><span class="icon-facebook2"></span></a>' : '') ?>
                    <?= (!empty($telegram_setting) ? '<a href="' . $telegram_setting . '" target="_blank"><span class="icon-paper-plane"></span></a>' : '') ?>
                    <?= (!empty($aparat_setting) ? '<a href="' . $aparat_setting . '" target="_blank"><span class="icon-film"></span></a>' : '') ?>
                    <?= (!empty($youtube_setting) ? '<a href="' . $youtube_setting . '" target="_blank"><span class="icon-youtube"></span></a>' : '') ?>

                </div>
            </div>
            <div class="copyright">
                © <?= jdate("Y") . ' ' . $lang[49] ?>
            </div>
		<div class="footer-enamad" style="text-align:center;margin-top:15px;">
            <a referrerpolicy="origin" target="_blank"
             href="https://trustseal.enamad.ir/?id=749843&Code=gJLkeOh0xBmZWtK6tu7DC6FQ3sol3etI">
              <img referrerpolicy="origin"
             src="https://trustseal.enamad.ir/logo.aspx?id=749843&Code=gJLkeOh0xBmZWtK6tu7DC6FQ3sol3etI"
             width="90"
             alt=""
             style="cursor:pointer">
             </a>
            </div>
        </div>
    </div>
</footer>
<?php if ($site_url == 'none@') { ?>
    <button id="chatToggle" class="btn btn-primary rounded-pill shadow">
        <img src="<?= $site_url ?>asset_site/images/robot.svg">
        <span><?= $lang[138] ?></span>
    </button>

    <div id="chatbox" class="d-flex flex-column d-none">
        <div class="text-white p-2 rounded-top text-right bg-chat-header">
            <?= $lang[139] ?>
        </div>
        <div class="chat-messages border border-top-0 flex-grow-1" id="chatMessages"></div>
        <div class="chat-input border border-top-0">
            <div class="input-group">
                <input type="text" id="userMessage" class="form-control" placeholder="<?= $lang[140] ?>" />
                <button class="btn btn-success" onclick="sendMessage()">ارسال</button>
            </div>
        </div>
    </div>
<?php } ?>
<script type="text/javascript">
    ! function() {
        var i = "R2zqwm",
            a = window,
            d = document;

        function g() {
            var g = d.createElement("script"),
                s = "https://www.goftino.com/widget/" + i,
                l = localStorage.getItem("goftino_" + i);
            g.async = !0, g.src = l ? s + "?o=" + l : s;
            d.getElementsByTagName("head")[0].appendChild(g);
        }
        "complete" === d.readyState ? g() : a.attachEvent ? a.attachEvent("onload", g) : a.addEventListener("load", g, !1);
    }();
</script>

<script src="<?= $site_url ?>asset_site/js/jquery-3.2.1.min.js"></script>
<script src="<?= $site_url ?>asset_site/js/bootstrap.bundle.js"></script>
<script src="<?= $site_url ?>asset_site/js/jquery-ui.min.js"></script>
<script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="<?= $site_url ?>asset_site/js/script_fa.js?ver=<?= rand(10, 1000) ?>"></script>
<script>
    <?php if ($site_url == 'testi') { ?>
        const toggleBtn = document.getElementById("chatToggle");
        const chatbox = document.getElementById("chatbox");
        const userInput = document.getElementById("userMessage");
        const messages = document.getElementById("chatMessages");

        toggleBtn.addEventListener("click", () => {
            chatbox.classList.toggle("d-none");
        });

        userInput.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                sendMessage();
            }
        });

        async function sendMessage() {
            const text = userInput.value.trim();
            if (!text) return;

            addMessage(text, "user");
            userInput.value = "";

            const res = await fetch("<?= $site_url ?>chat-api.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    message: text
                }),
            });

            if (!res.ok) {
                addMessage("Connect Server Error : ", "bot");
                return;
            }

            let data = {};
            try {
                data = await res.json();
            } catch (e) {
                addMessage("Response Problem ", "bot");
                return;
            }

            const reply = data.choices?.[0]?.message?.content || "Not Recive Data !";
            addMessage(reply, "bot");
        }

        function addMessage(msg, sender) {
            const div = document.createElement("div");
            div.className = `chat-message mb-2 p-2 rounded ${
        sender === "user" ? "bg-light text-end" : "bg-secondary text-white"
    }`;
            div.innerText = msg;
            messages.appendChild(div);
            messages.scrollTop = messages.scrollHeight;
        }
    <?php } ?>
    $(document).ready(function() {
        $('.customer').slick({
            dots: true,
            infinite: false,
            speed: 300,
            slidesToShow: 7,
            slidesToScroll: 1,
            prevArrow: false,
            nextArrow: false,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 3,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1
                    }
                }
            ]
        });


        $('.index-slider').slick({
            dots: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 4000,
            pauseOnFocus: false,
            infinite: true,
            loop: true,
            pauseOnHover: true,
            pauseOnDotsHover: true,
            arrows: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });


        $('.section-two').slick({
            dots: true,
            autoplay: true,
            autoplaySpeed: 3000,
            pauseOnFocus: false,
            infinite: true,
            loop: true,
            pauseOnHover: true,
            pauseOnDotsHover: true,
            speed: 300,
            arrows: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        $('.section-three').slick({
            dots: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 2000,
            pauseOnFocus: false,
            infinite: true,
            loop: true,
            pauseOnHover: true,
            pauseOnDotsHover: true,
            arrows: false,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        $('.section-four').slick({
            dots: true,
            speed: 300,
            autoplay: true,
            autoplaySpeed: 2000,
            pauseOnFocus: false,
            infinite: true,
            loop: true,
            pauseOnHover: true,
            pauseOnDotsHover: true,
            arrows: false,
            slidesToShow: 4,
            slidesToScroll: 1,
            responsive: [{
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
    });
    var controls = [
        'play',
        'progress',
        'captions',
        'mute',
        'volume',
        'airplay',
        'fullscreen',
        'settings'
    ];
    //var setting = ['captions', 'quality', 'speed', 'loop'];
    const players = Plyr.setup('video', {
        controls
    });
    const player = new Plyr('audio', {});
    window.player = player;
</script>
</body>

</html>
