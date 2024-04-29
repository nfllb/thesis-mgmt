<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Half Circle Progress Bar</title>
    <style>
        .progress-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            position: relative;
            width: 200px;
            height: 100px;
        }

        .progress-circle {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
        }

        .progress-background {
            fill: none;
            stroke: #ccc;
            stroke-width: 4;
        }

        .progress-bar {
            fill: none;
            stroke-width: 4;
            stroke-linecap: round;
        }

        .progress-text {
            fill: #333;
            font-size: 12px;
            font-family: Arial, sans-serif;
            text-anchor: middle;
            dominant-baseline: middle;
        }

        .label-completed,
        .label-in-progress,
        .label-not-started {
            font-size: 12px;
            font-family: Arial, sans-serif;
            fill: #333;
        }

        .completed-value {
            fill: green;
        }

        .in-progress-value {
            fill: yellow;
        }

        .not-started-value {
            fill: gray;
        }
    </style>
</head>

<body>
    <div class="progress-container">
        <svg class="progress-circle" viewBox="0 0 100 50">
            <circle class="progress-background" cx="50" cy="50" r="40"></circle>
            <circle class="progress-bar" cx="50" cy="50" r="40" transform="rotate(90 50 50)"></circle>
            <text class="progress-text" x="50" y="50">0%</text>
        </svg>

        <!-- Label for completed tasks -->
        <text class="label-completed" x="20" y="20">Completed: <tspan class="completed-value">0</tspan></text>

        <!-- Label for tasks in progress -->
        <text class="label-in-progress" x="20" y="35">In Progress: <tspan class="in-progress-value">0</tspan></text>

        <!-- Label for tasks not started -->
        <text class="label-not-started" x="20" y="50">Not Started: <tspan class="not-started-value">0</tspan></text>
    </div>




    <script>
        function setProgress(progress) {
            const circle = document.querySelector('.progress-bar');
            const text = document.querySelector('.progress-text');

            const radius = circle.getAttribute('r');
            const circumference = Math.PI * 2 * radius; // Full circumference

            const offset = circumference - (progress / 100) * circumference;

            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = offset;

            text.textContent = `${progress}%`;
        }


        // Example: Set progress to 70%
        setProgress(11);
    </script>
</body>

</html>