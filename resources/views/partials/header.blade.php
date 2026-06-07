<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NFL Pool</title>
        <style>
            .option-group {
                display: flex;
                gap: 10px;
            }

            .option-group input[type="radio"] {
                display: none;
            }

            .option-group label {
                padding: 10px 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background: #eee;
                cursor: pointer;
                transition: 0.2s;
            }

            .option-group input[type="radio"]:checked + label {
                background: red;
                color: white;
                border-color: red;
            }

            .pick-group input[type="radio"]:checked + label {
                background: #F2D3CE;
                color: white;
                border-color: #F2D3CE;
            }
            .pick-group label {
                padding: 5px 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background: #eee;
                cursor: pointer;
                /* transition: 0.2s; */
            }
            .pick-group-options {
                display: grid;
                grid-auto-flow: column;
                gap: 10px;
            }
        </style>
    </head>
    <body>