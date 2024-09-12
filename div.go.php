<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İlan Oyunu</title>
    <style>
        body {
            image-rendering: crisp-edges;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .plural-div {
            padding-top: 15px;
            padding-bottom: 15px;
            margin: 25px;
            background-color: rgb(55, 176, 212);
        }

        .p-div-one {
            color: #ffffff;
            margin-right: 104px;
        }

        .p-div-two {
            color: #ffffff;
            margin-left: 104px;
        }

        .go-dv {
            background-color: rgb(255, 238, 0);
        }

        #game-board {
            background-color: rgb(255, 255, 255);
            display: grid;
            grid-template-columns: repeat(10, 30px);
            grid-template-rows: repeat(10, 30px);
            gap: 1px;
        }

        .cell {
            width: 30px;
            height: 30px;
            background-color: #a611b9;
        }

        .cell.snake-head {
            background-color: rgb(245, 245, 245); /* İlanın başı */
        }

        .cell.snake-body {
            background-color: rgb(0, 0, 0); /* İlanın bədəni (qara) */
        }

        .cell.apple {
            background-color: rgb(0, 255, 0); /* Meyvə rəngi (yaşıl) */
        }

        #controls {
            margin-top: 20px;
            background-color: rgb(38, 53, 192);
        }

        button {
            color: #000000;
            background-color: rgb(255, 255, 255);
            margin: 13px;
            border-radius: 10px;
        }

        .button-hover:hover {
            color: #ffffff;
            background-color: rgb(0, 0, 0);
            transition: transform 125ms;
            transform: translateY(-10px);
        }
        .message {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="plural-div">
        <span class="p-div-one">Xal: 0</span>
        <span class="p-div-two">Ölüm: 0</span>
    </div>
    <div class="go-dv">
        <div class="game-board" id="game-board">
        <!-- 10x10 şəbəkə üçün kvadratlar JavaScript tərəfindən əlavə ediləcək -->
        </div>
    </div>
    <div id="controls">
        <button class="button-hover" id="left">Sola</button>
        <button class="button-hover" id="down">Aşağı</button>
        <button class="button-hover" id="up">Yuxarı</button>
        <button class="button-hover" id="right">Sağa</button>
    </div>

    <div id="message" class="message">İlan uduzdu! Sizin xalınız: <span id="final-score">0</span><br><br><button onclick="restartGame()">Yenidən Başla</button></div>


    <script>
        // Oyun taxtasının ölçüsü və digər başlanğıc məlumatları
        const boardSize = 10; // Taxtanın ölçüsü (10x10)
        const cells = []; // Kvadratları saxlamaq üçün massiv
        let snake = [{x: 4, y: 4}]; // İlanın başlanğıc mövqeyi
        let direction = 'center'; // İlanın hansı istiqamətdə hərəkət edəcəyini göstərir
        let apple = {x: 0, y: 0}; // Meyvənin mövqeyi
        let score = 0; // İlanın xalı

        // Oyun taxtasını yaradın
        function createBoard() {
            const gameBoard = document.getElementById('game-board');
            for (let i = 0; i < boardSize * boardSize; i++) {
                const cell = document.createElement('div'); // Yeni kvadrat yaradın
                cell.className = 'cell'; // Kvadratyə sinif əlavə edin
                gameBoard.appendChild(cell); // Kvadratni oyun taxtasına əlavə edin
                cells.push(cell); // Kvadratni massivə əlavə edin
            }
            placeApple(); // Meyvəni yerə qoyun
            updateBoard(); // Taxtanı yeniləyin
        }

        // Taxtanı yeniləyir, ilanı və meyvəni göstərir
        function updateBoard() {
            cells.forEach(cell => cell.classList.remove('snake-head', 'snake-body', 'apple')); // Bütün kvadratlardan sinifləri silin
            snake.forEach((segment, index) => {
                const indexCell = segment.y * boardSize + segment.x; // İlanın hissəsinin taxtadakı indeksini hesablayın
                if (index === 0) {
                    cells[indexCell].classList.add('snake-head'); // İlanın başını işarələyin
                } else {
                    cells[indexCell].classList.add('snake-body'); // İlanın bədənini işarələyin
                }
            });
            const appleIndex = apple.y * boardSize + apple.x; // Meyvənin taxtadakı indeksini hesablayın
            cells[appleIndex].classList.add('apple'); // İlgili kvadratyə 'apple' sinifini əlavə edin
            document.querySelector('.p-div-one').textContent = `Xal: ${score}`; // Xalı yeniləyin
        }

        // Meyvəni təsadüfi yerə qoyur
        function placeApple() {
            apple.x = Math.floor(Math.random() * boardSize);
            apple.y = Math.floor(Math.random() * boardSize);
        }

        // İlanı hərəkət etdirir
        function moveSnake() {
            const head = {...snake[0]}; // İlanın başını kopyalayın
            switch (direction) {
                case 'up':
                    head.y--; // Yuxarı hərəkət edin
                    break;
                case 'down':
                    head.y++; // Aşağı hərəkət edin
                    break;
                case 'left':
                    head.x--; // Sola hərəkət edin
                    break;
                case 'right':
                    head.x++; // Sağa hərəkət edin
                    break;
            }

            // Taxtanın kənarına çıxmağı qarşısını alır
            // if (head.x < 0 || head.x >= boardSize || head.y < 0 || head.y >= boardSize) {
            //     head.x = (head.x + boardSize) % boardSize;
            //     head.y = (head.y + boardSize) % boardSize;
            // }

            // İlanın öz bədəni ilə toqquşmasını yoxlayır
            for (let i = 1; i < snake.length; i++) {
                if (snake[i].x === head.x && snake[i].y === head.y) {
                    endGame(); // Oyun bitir
                    return;
                }
            }

            // İlanın yeni başı meyvə ilə üst-üstə düşürsə
            if (head.x === apple.x && head.y === apple.y) {
                score++; // Xalı artır
                placeApple(); // Yeni meyvə yerini qoy
            } else {
                snake.pop(); // İlanın quyruğunu çıxarın
            }

            snake.unshift(head); // Yeni başı ilanının başına əlavə edin
            updateBoard(); // Taxtanı yeniləyin
        }

            // Oyun bitir və mesaj göstərir
            function endGame() {
            document.getElementById('final-score').textContent = score;
            document.getElementById('message').style.display = 'block';
            direction = 'center'; // İlanın hərəkətini dayandırın
            score = 0; // Xalı sıfırlayın
        }

                // Oyunu yenidən başlat
                function restartGame() {
            document.getElementById('message').style.display = 'none';
            snake = [{ x: 4, y: 4 }]; // İlanı yenidən başlanğıc mövqeyinə qaytarın
            placeApple(); // Yeni meyvə qoyun
            updateBoard(); // Taxtanı yeniləyin
            setInterval(moveSnake, 500); // İlanın hərəkət etmə tezliyini müəyyən edir (500 ms)
        }

        // Oyun bitir və mesaj göstərir
        function endGame() {
            alert(`Oyun bitdi! Sizin xalınız: ${score}`);
            score = 0; // Xalı sıfırlayın
            direction = 'center'; // İlanın hərəkətini dayandırın
            snake = [{x: 4, y: 4}]; // İlanı yenidən başlanğıc mövqeyinə qaytarın
            placeApple(); // Yeni meyvə qoyun
            updateBoard(); // Taxtanı yeniləyin
        }

        // Butonlara klikləmə hadisələri əlavə edir
        document.getElementById('up').addEventListener('click', () => {
            if (direction !== 'down') direction = 'up'; // Əgər ilan aşağı hərəkət edirsə, yuxarı hərəkət etsin
        });
        document.getElementById('down').addEventListener('click', () => {
            if (direction !== 'up') direction = 'down'; // Əgər ilan yuxarı hərəkət edirsə, aşağı hərəkət etsin
        });
        document.getElementById('left').addEventListener('click', () => {
            if (direction !== 'right') direction = 'left'; // Əgər ilan sağa hərəkət edirsə, sola hərəkət etsin
        });
        document.getElementById('right').addEventListener('click', () => {
            if (direction !== 'left') direction = 'right'; // Əgər ilan sola hərəkət edirsə, sağa hərəkət etsin
        });

        // Klaviatura hadisələri əlavə edir
        document.addEventListener('keydown', event => {
            switch (event.key) {
                case 'ArrowUp':
                case 'w':
                    if (direction !== 'down') direction = 'up';
                    break;
                case 'ArrowDown':
                case 's':
                    if (direction !== 'up') direction = 'down';
                    break;
                case 'ArrowLeft':
                case 'a':
                    if (direction !== 'right') direction = 'left';
                    break;
                case 'ArrowRight':
                case 'd':
                    if (direction !== 'left') direction = 'right';
                    break;
            }
        });

        createBoard(); // Taxtanı yaradın
        setInterval(moveSnake, 500); // İlanın hərəkət etmə tezliyini müəyyən edir (500 ms)
    </script>
</body>
</html>
