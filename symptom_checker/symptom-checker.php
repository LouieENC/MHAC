<?php
    session_start();

    include '../includes/db_connect.php';

    $timeout_duration = 1800;

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: ../login-signup.php");
        exit();
    }

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: ../login-signup.php?message=Session expired. Please log in again.");
        exit();
    }

    $_SESSION['last_activity'] = time();

    // Check user role
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
        header("Location: ../access_denied.php");
        exit();
    }

    $student_id = $_SESSION['student_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: white;
            padding: 20px 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 400px;
        }
        .form-container h3 {
            font-size: 16px;
            color: #777;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .form-group input[type="text"],
        .form-group select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .suggestion-input {
            position: relative;
        }
        .suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 10;
            background-color: #fff;
            border: 1px solid #ccc;
            border-top: none;
            max-height: 150px;
            overflow-y: auto;
            display: none;
        }
        .suggestions div {
            padding: 8px;
            cursor: pointer;
        }
        .suggestions div:hover {
            background-color: #f0f0f0;
        }
        .add-symptom-btn {
            margin: 10px 0;
            padding: 8px;
            font-size: 14px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .submit-btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h3>Disclaimer: Please contact a professional doctor for a proper diagnosis</h3>
    <form id="symptom-form" method="POST">
        <div class="form-group suggestion-input">
            <label>Symptoms</label>
            <input type="text" name="symptom[]" placeholder="Add Symptom..." 
                   onclick="fetchSymptoms(this)" oninput="filterSuggestions(this)" autocomplete="off"/>
            <select name="severity[]" class="severity-select">
                <option value="1">Mild</option>
                <option value="2">Moderate</option>
                <option value="3">Severe</option>
            </select>
            <div class="suggestions"></div>
        </div>
        
        <div id="additional-symptoms"></div>
        
        <button type="button" class="add-symptom-btn" onclick="addSymptomInput()">Add More Symptoms</button>
        <button type="submit" class="submit-btn">See Results</button>
        <br>
        <br>
        <a href="../patient/student_dashboard.php" class="submit-btn">Go Back</a>

    </form>

    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $selectedSymptoms = array_unique($_POST['symptom']);
            $severityLevels = $_POST['severity'];

            require '../includes/db_connect.php';

            $symptomsSeverityData = [];
            foreach ($selectedSymptoms as $index => $symptom) {
                $symptomsSeverityData[$symptom] = (int)$severityLevels[$index];
            }

            $symptomsQuery = implode(",", array_map(fn($symptom) => "'$symptom'", $selectedSymptoms));

            // Calculate actual relevance score for each condition
            $query = "SELECT c.condition_id, c.name, c.description, c.health_tips, 
                            SUM(cs.severity_weight * {$symptomsSeverityData[$symptom]}) AS relevance,
                            COUNT(cs.symptom_id) * 3 AS max_relevance  -- Max relevance assumes all symptoms are 'Severe'
                    FROM Conditions c
                    JOIN ConditionSymptoms cs ON c.condition_id = cs.condition_id
                    JOIN Symptoms s ON cs.symptom_id = s.symptom_id
                    WHERE s.name IN ($symptomsQuery)
                    GROUP BY c.condition_id
                    HAVING relevance > 1  -- Filter for relevant conditions only
                    ORDER BY relevance DESC, c.name";

            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                echo "<h3>Possible Conditions:</h3><ul>";
                while ($row = $result->fetch_assoc()) {
                    $relevancePercentage = ($row['relevance'] / $row['max_relevance']) * 100;
                    echo "<li><strong>{$row['name']}</strong>: {$row['description']}<br>
                        <em>Health Tips:</em> {$row['health_tips']}<br>
                        <strong>Probability:</strong> " . round($relevancePercentage, 2) . "%</li>";
                }
                echo "</ul>";
                echo "<br>";
            } else {
                echo "<p>No matching conditions found.</p>";
                echo "<p>Otherwise, please be careful with spellings and also, be specific i.e. use one word e.g. 'headache'</p>";
            }
            $conn->close();
        }
    ?>
</div>

<script>
    function addSymptomInput() {
        const additionalSymptoms = document.getElementById('additional-symptoms');
        const newSymptomDiv = document.createElement('div');
        newSymptomDiv.classList.add('form-group', 'suggestion-input');
        
        const newSymptomInput = document.createElement('input');
        newSymptomInput.type = 'text';
        newSymptomInput.placeholder = 'Add more symptoms...';
        newSymptomInput.oninput = function() { fetchSymptoms(newSymptomInput); };

        const severitySelect = document.createElement('select');
        severitySelect.name = 'severity[]';
        severitySelect.classList.add('severity-select');
        severitySelect.innerHTML = `<option value="1">Mild</option><option value="2">Moderate</option><option value="3">Severe</option>`;
        
        const suggestionsDiv = document.createElement('div');
        suggestionsDiv.classList.add('suggestions');
        
        newSymptomDiv.appendChild(newSymptomInput);
        newSymptomDiv.appendChild(severitySelect);
        newSymptomDiv.appendChild(suggestionsDiv);
        additionalSymptoms.appendChild(newSymptomDiv);
    }

    function fetchSymptoms(input) {
        let query = input.value;
        fetch(`fetch_symptom.php?q=${query}`)
            .then(response => response.json())
            .then(data => showSuggestions(data, input));
    }

    function showSuggestions(suggestions, input) {
        const suggestionsDiv = input.nextElementSibling.nextElementSibling;
        suggestionsDiv.innerHTML = '';
        suggestions.forEach(suggestion => {
            const suggestionElement = document.createElement('div');
            suggestionElement.textContent = suggestion;
            suggestionElement.onclick = () => {
                input.value = suggestion;
                suggestionsDiv.style.display = 'none';
            };
            suggestionsDiv.appendChild(suggestionElement);
        });
        suggestionsDiv.style.display = 'block';
    }
</script>

</body>
</html>