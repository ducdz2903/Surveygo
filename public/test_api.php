<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Survey Submit API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }

        .result {
            margin-top: 20px;
            white-space: pre-wrap;
            font-family: monospace;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Test Survey Submit API</h1>

        <div class="row mt-4">
            <div class="col-md-6">
                <form id="testForm">
                    <div class="mb-3">
                        <label for="surveyId" class="form-label">Survey ID:</label>
                        <input type="number" id="surveyId" class="form-control" value="1">
                    </div>
                    <div class="mb-3">
                        <label for="userId" class="form-label">User ID:</label>
                        <input type="number" id="userId" class="form-control" value="1">
                    </div>
                    <div class="mb-3">
                        <label for="answers" class="form-label">Answers (JSON):</label>
                        <textarea id="answers" class="form-control" rows="6">{
  "1": "Buổi sáng",
  "2": "3",
  "3": "[\"5\", \"6\"]"
}</textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="submitTest()">Test Submit</button>
                </form>
            </div>
            <div class="col-md-6">
                <div id="result" class="result"></div>
            </div>
        </div>
    </div>

    <script>
        async function submitTest() {
            const surveyId = document.getElementById('surveyId').value;
            const userId = document.getElementById('userId').value;
            const resultDiv = document.getElementById('result');

            try {
                const answersText = document.getElementById('answers').value;
                const answers = JSON.parse(answersText);

                const response = await fetch(`/api/surveys/${surveyId}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        userId: parseInt(userId),
                        answers: answers
                    })
                });

                const result = await response.json();
                resultDiv.textContent = JSON.stringify(result, null, 2);

            } catch (error) {
                resultDiv.textContent = 'Error: ' + error.message;
            }
        }
    </script>
</body>

</html>