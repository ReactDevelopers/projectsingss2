<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Titillium+Web' rel='stylesheet' type='text/css'>

    <style>
        body, html {
            /*background: url('/img/spark-bg.png');*/
            /*background-repeat: repeat;*/
            /*background-size: 300px 200px;*/
            height: 100%;
            margin: 0;
        }

        .full-height {
            min-height: 100%;
        }

        .flex-column {
            display: flex;
            flex-direction: column;
        }

        .flex-fill {
            flex: 1;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }


        .text-center {
            text-align: center;
        }

        .links {
            padding: 1em;
            text-align: right;
        }

        .links a {
            text-decoration: none;
        }

        .links button {
            /*background-color: #3097D1;*/
            background-color: #1f1f27;
            border: 0;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-family: 'Titillium Web';
            font-size: 12px;
            /*font-weight: 600;*/
            padding: 15px;
            text-transform: uppercase;
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="full-height flex-column">
        <nav class="links">
            <a href="/login" style="margin-right: 15px;">
                <button>
                    <?php echo e(__('Sign In')); ?>

                </button>
            </a>

            <a href="/register">
                <button>
                    <?php echo e(__('Register')); ?>

                </button>
            </a>
        </nav>

        <div class="flex-fill flex-center">
            <h1 class="text-center">
                <img src="/images/f1-logo-large.png">
            </h1>
        </div>
    </div>
</body>
</html>