<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>烟花和消息动画</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100%;
        }
        #fireworksCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        #message, #messages2 {
            color: white;
            font-size: 36px;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            background-color: black;
        }
    </style>
</head>
<body>
    <canvas id="fireworksCanvas"></canvas>
    <div id="container">
        <h1 id="message"></h1>
        <h1 id="messages2"></h1>
    </div>

    <script>
        let particles = [];
        let fireworksCount = 10; // 烟花数量
        let fireworksLaunched = 0;
        let heartAnimationCompleted = false;

        class Particle {
            constructor(x, y, color, velocity) {
                this.x = x;
                this.y = y;
                this.color = color;
                this.velocity = velocity;
                this.alpha = 1;
            }

            draw() {
                const canvas = document.getElementById('fireworksCanvas');
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.globalAlpha = this.alpha;
                ctx.beginPath();
                ctx.arc(this.x, this.y, 3, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
                ctx.restore();
            }

            update() {
                this.x += this.velocity.x;
                this.y += this.velocity.y;
                this.alpha -= 0.01; // 每次更新时，减少透明度
            }
        }

        const explosionSound = new Audio('http://localhost/dashboard/fireworks/assets/12316.mp3');

        // 确保音频文件已经加载完毕
        explosionSound.addEventListener('canplaythrough', function() {
            console.log('Audio ready to play');
        });

        // 播放爆炸音效
        function playExplosionSound() {
            // 确保音频已经加载完毕后播放
            if (explosionSound.readyState >= 3) {
                explosionSound.play().catch(function(e) {
                    console.error('Error playing explosion sound:', e);
                });
            } else {
                console.log('Audio is not ready, waiting to load...');
                explosionSound.addEventListener('canplaythrough', function() {
                    explosionSound.play();
                });
            }
        }

        function createFirework(x, y) {
            const colors = ['#ff004c', '#ffff00', '#00ffcc', '#ff9900', '#66ccff'];
            for (let i = 0; i < 50; i++) {
                const angle = Math.random() * Math.PI * 2;
                const speed = Math.random() * 3 + 2;
                particles.push(new Particle(x, y, colors[Math.floor(Math.random() * colors.length)], {
                    x: Math.cos(angle) * speed,
                    y: Math.sin(angle) * speed
                }));
            }

            // 播放爆炸音效
            playExplosionSound();  // 调用播放音效的函数
        }

        function launchFireworks(mode) {
            const canvas = document.getElementById('fireworksCanvas');
            const ctx = canvas.getContext('2d');

            if (mode === 3) {
                showMessages();
                // 第三轮：从屏幕中心斜向发射，随机次数发射
                const maxFireworks = 25; // 固定发射次数，确保烟花可以发射多次
            
                if (fireworksLaunched < maxFireworks) {
                    const angle = Math.random() * Math.PI / 4 - Math.PI / 8; // 随机生成斜角
                    const velocity = { 
                        x: Math.cos(angle) * 6, 
                        y: -Math.sin(angle) * 6 
                    };
            
                    const x = Math.random() * canvas.width;
                    const y = Math.random() * canvas.height;
            
                    createFirework(x, y); 
            
                    fireworksLaunched++; 
            
                    const randomDelay = Math.random() * 300 + 200;
            
                    setTimeout(() => launchFireworks(3), randomDelay); // 控制发射延迟
                } else {
                    launchFireworks(5); // 切换到爱心效果
                }
            } else if (mode === 5) {
                // 爱心效果
                if (!heartAnimationCompleted) {
                    heartAnimationCompleted = true;
                    
                    setTimeout(() => {
                        let heartParticles = [];
                        const heartColor = '#ff69b4';
                        const heartRadius = 10;
                
                        for (let t = 0; t < Math.PI * 2; t += 0.1) {
                            const x = 16 * Math.pow(Math.sin(t), 3) * heartRadius + window.innerWidth / 2;
                            const y = -(13 * Math.cos(t) - 5 * Math.cos(2 * t) - 2 * Math.cos(3 * t) - Math.cos(4 * t)) * heartRadius + window.innerHeight / 2;
                            const angle = Math.random() * Math.PI * 2;
                            const speed = Math.random() * 2 + 1;
                            const velocity = {
                                x: Math.cos(angle) * speed,
                                y: Math.sin(angle) * speed
                            };
                
                            heartParticles.push(new Particle(x, y, heartColor, velocity));
                        }
                
                        const interval = setInterval(() => {
                            heartParticles.forEach(p => {
                                p.update();
                                p.draw();
                            });
                
                            heartParticles = heartParticles.filter(p => p.alpha > 0);
                
                            if (heartParticles.length === 0) {
                                clearInterval(interval);
                                showMessages2();  // Trigger the next message set after animation ends
                            }
                        }, 15);
                    }, 1000); // 延迟启动爱心效果
                }
            } else {
                // 其他模式，继续发射烟花
                if (fireworksLaunched < fireworksCount) {
                    const x = Math.random() * canvas.width;
                    const y = Math.random() * canvas.height; 
                    createFirework(x, y);
                    fireworksLaunched++;
                    setTimeout(() => launchFireworks(mode), 500); // 延迟重复发射
                }
            }
        }

        function showMessages2() {
            const messages2 = [
                "",
                "心怡你还记得去年的今天吗", // 第一个消息
                "我们一起看了烟花", // 倒计时消息
                "虽然今年看的烟花没有声音", 
                "但是，我们也算是一起看了烟花呢",
                "心怡，希望你不嫌弃今年的烟花 嘿~~",
                "黄心怡！",
                "我",
                "爱",
                "你！",
                "嘿嘿~ ^v^",
                "结束啦 谢谢观看"
            ];

            let index2 = 0; // 当前消息的索引
            const messageElement2 = document.getElementById("messages2");

            function displayMessages2() {
                if (index2 < messages2.length) {
                    messageElement2.textContent = messages2[index2];
                    messageElement2.style.display = "block"; // 显示消息
                    let delay = 2000; // 默认延迟 2 秒
                    if (index2 === 1) {
                        delay = 2000; // 第一个消息显示 3 秒
                    }
                    index2++; // 更新索引
                    setTimeout(displayMessages2, delay); // 设置延时调用
                } else {
                    // 所有消息显示完，隐藏消息框
                    messageElement2.style.display = "none";
                }
            }

            displayMessages2(); // 开始显示消息
        }

        function animate() {
            const canvas = document.getElementById('fireworksCanvas');
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = 'rgba(0, 0, 0, 0.2)';
            ctx.fillRect(0, 0, canvas.width, canvas.height); // 绘制背景

            particles = particles.filter(p => p.alpha > 0); // 过滤掉透明度为 0 的粒子
            particles.forEach(p => {
                p.update();
                p.draw(); // 绘制每个粒子
            });

            requestAnimationFrame(animate); // 循环调用
        }

        // 初始化 canvas 的大小
        function resizeCanvas() {
            const canvas = document.getElementById('fireworksCanvas');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        // 调用 resizeCanvas 函数，并监听窗口大小变化
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // 显示消息并启动烟花
        const messages = [
            "",
            "心怡，准备好了吗 嘿嘿🤭", // 第一个消息
            "3", // 倒计时消息
            "2", 
            "1"
        ];

        let index = 0; // 当前消息的索引

        function showMessages() {
            const messageElement = document.getElementById("message");

            if (index < messages.length) {
                messageElement.textContent = messages[index];
                messageElement.style.display = "block"; // 显示消息

                let delay = 1000;
                if (index === 1) {
                    delay = 2000; // 第一个消息显示 3 秒
                }

                index++; // 更新索引
                setTimeout(showMessages, delay); // 设置延时调用
            } else {
                // 所有消息已显示，开始烟花
                messageElement.style.display = "none"; // 隐藏文字
                launchFireworks(3); // 启动第三轮烟花
                animate(); // 启动动画循环
            }
        }
        showMessages(); // 显示倒计时消息
    </script>
</body>
</html>
