// Sample symptom data (you can expand this as needed)
const symptomsDatabase = {
    "fever": ["flu", "cold", "COVID-19"],
    "cough": ["flu", "cold", "COVID-19", "pneumonia"],
    "headache": ["migraine", "tension headache", "flu"],
    "nausea": ["gastroenteritis", "food poisoning"],
    "sore throat": ["flu", "cold", "strep throat"],
    "shortness of breath": ["asthma", "COVID-19", "pneumonia"]
};

// Function to open the modal
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

// Function to close the modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Function to send user input and get symptoms
function sendMessage() {
    const selectedSymptoms = Array.from(document.getElementById('symptom-select').selectedOptions)
        .map(option => option.value);
    const chatLog = document.getElementById('chatLog');
    const recommendationLog = document.getElementById('recommendationLog');

    if (selectedSymptoms.length > 0) {
        // Add user message to chat log
        chatLog.innerHTML += `<div class="user-message"><strong>You:</strong> ${selectedSymptoms.join(', ')}</div>`;

        // Analyze symptoms
        const results = analyzeSymptoms(selectedSymptoms);
        
        // Display results
        if (results.length > 0) {
            chatLog.innerHTML += `<div class="bot-message"><strong>Bot:</strong> Based on your symptoms, you might have: ${results.join(', ')}.</div>`;
            recommendActions(results);
        } else {
            chatLog.innerHTML += `<div class="bot-message"><strong>Bot:</strong> Sorry, I couldn't find any matching conditions for your symptoms.</div>`;
        }
        
        // Clear the input field
        document.getElementById('symptom-select').selectedIndex = -1; // Reset selections
    }
}

// Function to analyze user symptoms
function analyzeSymptoms(selectedSymptoms) {
    let possibleConditions = new Set();

    selectedSymptoms.forEach(symptom => {
        if (symptomsDatabase[symptom]) {
            symptomsDatabase[symptom].forEach(condition => possibleConditions.add(condition));
        }
    });

    return Array.from(possibleConditions);
}

// Function to recommend actions based on identified conditions
function recommendActions(conditions) {
    const recommendationLog = document.getElementById('recommendationLog');
    recommendationLog.innerHTML = ''; // Clear previous recommendations

    conditions.forEach(condition => {
        let recommendation;
        switch (condition) {
            case 'flu':
                recommendation = "You may want to rest and stay hydrated. Consider over-the-counter medications.";
                break;
            case 'cold':
                recommendation = "Ensure you take care of yourself with warm fluids. Monitor your symptoms.";
                break;
            case 'COVID-19':
                recommendation = "Consider getting tested for COVID-19. Follow health guidelines.";
                break;
            case 'pneumonia':
                recommendation = "Seek medical attention as pneumonia can be serious.";
                break;
            case 'gastroenteritis':
                recommendation = "Stay hydrated and monitor your food intake. Consider visiting a doctor if symptoms persist.";
                break;
            case 'food poisoning':
                recommendation = "Drink plenty of fluids. Consult a doctor if symptoms worsen.";
                break;
            case 'asthma':
                recommendation = "Ensure you have your inhaler handy and avoid triggers. Consult your doctor.";
                break;
            default:
                recommendation = "It's best to consult a healthcare professional.";
                break;
        }
        recommendationLog.innerHTML += `<div><strong>${condition}</strong>: ${recommendation}</div>`;
    });
}


