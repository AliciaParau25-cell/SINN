<?php
include 'db_game4.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $jugador = $_POST['jugador'];
    $resultado = $_POST['resultado'];

    $sql = "INSERT INTO resultados (jugador, resultado)
            VALUES ('$jugador', '$resultado')";

    $conn->query($sql);

    exit();
}

$ranking = $conn->query("
    SELECT jugador, COUNT(*) as victorias
    FROM resultados
    WHERE resultado = 'Victoria'
    GROUP BY jugador
    ORDER BY victorias DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Tres en Raya</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        body{
            background:#0f172a;
            color:white;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
        }

        .container{
            width:900px;
            max-width:95%;
            display:grid;
            grid-template-columns:2fr 1fr;
            gap:30px;
        }

        .game-box{
            background:#1e293b;
            padding:30px;
            border-radius:20px;
            box-shadow:0 0 25px rgba(0,0,0,0.4);
            text-align:center;
        }

        h1{
            margin-bottom:20px;
            color:#38bdf8;
        }

        .status{
            margin-bottom:20px;
            font-size:20px;
        }

        .board{
            display:grid;
            grid-template-columns:repeat(3, 1fr);
            gap:10px;
            margin:auto;
            width:320px;
        }

        .cell{
            width:100px;
            height:100px;
            background:#334155;
            border-radius:15px;
            display:flex;
            justify-content:center;
            align-items:center;
            font-size:50px;
            cursor:pointer;
            transition:0.2s;
        }

        .cell:hover{
            background:#475569;
        }

        button{
            margin-top:25px;
            padding:12px 20px;
            border:none;
            border-radius:10px;
            background:#38bdf8;
            color:white;
            font-size:16px;
            cursor:pointer;
        }

        button:hover{
            background:#0ea5e9;
        }

        .ranking{
            background:#1e293b;
            padding:25px;
            border-radius:20px;
            box-shadow:0 0 25px rgba(0,0,0,0.4);
        }

        .ranking h2{
            margin-bottom:20px;
            color:#38bdf8;
        }

        .ranking ul{
            list-style:none;
        }

        .ranking li{
            padding:12px;
            margin-bottom:10px;
            background:#334155;
            border-radius:10px;
        }

        input{
            width:100%;
            padding:12px;
            margin-bottom:20px;
            border:none;
            border-radius:10px;
            outline:none;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="game-box">

        <h1>❌⭕ Tres en Raya</h1>

        <input type="text" id="playerName" placeholder="Nombre del jugador">

        <div class="status" id="status">
            Turno de X
        </div>

        <div class="board" id="board">

            <div class="cell" data-index="0"></div>
            <div class="cell" data-index="1"></div>
            <div class="cell" data-index="2"></div>

            <div class="cell" data-index="3"></div>
            <div class="cell" data-index="4"></div>
            <div class="cell" data-index="5"></div>

            <div class="cell" data-index="6"></div>
            <div class="cell" data-index="7"></div>
            <div class="cell" data-index="8"></div>

        </div>

        <button onclick="resetGame()">
            Reiniciar partida
        </button>

    </div>

    <div class="ranking">

        <h2>🏆 Ranking</h2>

        <ul>

            <?php
            while($row = $ranking->fetch_assoc()){
                echo "<li>" . $row['jugador'] . " - " . $row['victorias'] . " victorias</li>";
            }
            ?>

        </ul>

    </div>

</div>

<script>

    const cells = document.querySelectorAll(".cell");
    const statusText = document.getElementById("status");

    let currentPlayer = "X";
    let gameActive = true;

    let gameState = [
        "", "", "",
        "", "", "",
        "", "", ""
    ];

    const winConditions = [
        [0,1,2],
        [3,4,5],
        [6,7,8],

        [0,3,6],
        [1,4,7],
        [2,5,8],

        [0,4,8],
        [2,4,6]
    ];

    cells.forEach(cell => {
        cell.addEventListener("click", cellClick);
    });

    function cellClick(){

        const index = this.dataset.index;

        if(gameState[index] !== "" || !gameActive){
            return;
        }

        gameState[index] = currentPlayer;
        this.textContent = currentPlayer;

        checkWinner();
    }

    function checkWinner(){

        let won = false;

        for(let condition of winConditions){

            let a = gameState[condition[0]];
            let b = gameState[condition[1]];
            let c = gameState[condition[2]];

            if(a === "" || b === "" || c === ""){
                continue;
            }

            if(a === b && b === c){
                won = true;
                break;
            }
        }

        if(won){

            statusText.textContent = `Jugador ${currentPlayer} gana`;

            saveResult();

            gameActive = false;
            return;
        }

        if(!gameState.includes("")){

            statusText.textContent = "Empate";

            gameActive = false;
            return;
        }

        currentPlayer = currentPlayer === "X" ? "O" : "X";

        statusText.textContent = `Turno de ${currentPlayer}`;
    }

    function resetGame(){

        gameState = [
            "", "", "",
            "", "", "",
            "", "", ""
        ];

        currentPlayer = "X";
        gameActive = true;

        statusText.textContent = "Turno de X";

        cells.forEach(cell => {
            cell.textContent = "";
        });
    }

    function saveResult(){

        let player = document.getElementById("playerName").value;

        if(player.trim() === ""){
            player = "Anónimo";
        }

        fetch("game4.php", {
            method:"POST",
            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body:`jugador=${player}&resultado=Victoria`
        });
    }

</script>

</body>
</html>