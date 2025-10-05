<!DOCTYPE html>
<html>
<head>
    <title>Messaging System Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        button { margin: 5px; padding: 10px 15px; }
        #results { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #f0f0f0; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>JB Printing Services - Messaging System Debug Tool</h1>
    
    <div class="test-section">
        <h2>1. Database Setup Test</h2>
        <button onclick="testDatabase()">Test Database Connection</button>
        <button onclick="setupTables()">Setup/Check Tables</button>
        <div id="dbResults"></div>
    </div>
    
    <div class="test-section">
        <h2>2. API Endpoint Tests</h2>
        <button onclick="testGetConversationsAdmin()">Test Admin Conversations</button>
        <button onclick="testGetConversationsUser()">Test User Conversations</button>
        <button onclick="testCreateConversation()">Test Create Conversation</button>
        <div id="apiResults"></div>
    </div>
    
    <div class="test-section">
        <h2>3. Sample Data Tests</h2>
        <button onclick="createSampleData()">Create Sample Data</button>
        <button onclick="viewAllData()">View All Data</button>
        <div id="dataResults"></div>
    </div>
    
    <div class="test-section">
        <h2>4. Frontend JavaScript Tests</h2>
        <button onclick="testFrontendFunctions()">Test Frontend Functions</button>
        <div id="frontendResults"></div>
    </div>
    
    <div id="results"></div>

    <script>
        function log(message, type = 'info') {
            const results = document.getElementById('results');
            const className = type;
            results.innerHTML += `<div class="${className}">[${new Date().toLocaleTimeString()}] ${message}</div>`;
            results.scrollTop = results.scrollHeight;
        }

        function logTo(elementId, message, type = 'info') {
            const element = document.getElementById(elementId);
            const className = type;
            element.innerHTML += `<div class="${className}">${message}</div>`;
        }

        async function testDatabase() {
            log('Testing database connection...', 'info');
            try {
                const response = await fetch('test_messaging_system.php');
                const text = await response.text();
                logTo('dbResults', text);
                log('Database test completed', 'success');
            } catch (error) {
                log('Database test failed: ' + error.message, 'error');
                logTo('dbResults', 'Error: ' + error.message, 'error');
            }
        }

        async function setupTables() {
            log('Setting up messaging tables...', 'info');
            try {
                const response = await fetch('setup_messaging_system.php');
                const text = await response.text();
                logTo('dbResults', text);
                log('Table setup completed', 'success');
            } catch (error) {
                log('Table setup failed: ' + error.message, 'error');
                logTo('dbResults', 'Error: ' + error.message, 'error');
            }
        }

        async function testGetConversationsAdmin() {
            log('Testing admin conversations API...', 'info');
            try {
                const url = 'components/messaging_api.php?action=get_conversations&user_type=admin&user_id=1';
                const response = await fetch(url);
                const text = await response.text();
                
                logTo('apiResults', `<strong>Admin Conversations API Response:</strong><br><pre>${text}</pre>`);
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('Admin conversations API working - Found ' + data.conversations.length + ' conversations', 'success');
                    } else {
                        log('Admin conversations API error: ' + data.error, 'error');
                    }
                } catch (parseError) {
                    log('Admin conversations API returned invalid JSON', 'error');
                }
            } catch (error) {
                log('Admin conversations API test failed: ' + error.message, 'error');
            }
        }

        async function testGetConversationsUser() {
            log('Testing user conversations API...', 'info');
            try {
                const url = 'components/messaging_api.php?action=get_conversations&user_type=user&user_id=1';
                const response = await fetch(url);
                const text = await response.text();
                
                logTo('apiResults', `<strong>User Conversations API Response:</strong><br><pre>${text}</pre>`);
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('User conversations API working - Found ' + data.conversations.length + ' conversations', 'success');
                    } else {
                        log('User conversations API error: ' + data.error, 'error');
                    }
                } catch (parseError) {
                    log('User conversations API returned invalid JSON', 'error');
                }
            } catch (error) {
                log('User conversations API test failed: ' + error.message, 'error');
            }
        }

        async function testCreateConversation() {
            log('Testing create conversation API...', 'info');
            try {
                const formData = new FormData();
                formData.append('action', 'create_conversation');
                formData.append('user_id', '1');
                formData.append('admin_id', '1');
                
                const response = await fetch('components/messaging_api.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                
                logTo('apiResults', `<strong>Create Conversation API Response:</strong><br><pre>${text}</pre>`);
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        log('Create conversation API working - Conversation ID: ' + data.conversation_id, 'success');
                    } else {
                        log('Create conversation API error: ' + data.error, 'error');
                    }
                } catch (parseError) {
                    log('Create conversation API returned invalid JSON', 'error');
                }
            } catch (error) {
                log('Create conversation API test failed: ' + error.message, 'error');
            }
        }

        async function createSampleData() {
            log('Creating sample messaging data...', 'info');
            try {
                const response = await fetch('create_sample_messaging_data.php');
                const text = await response.text();
                logTo('dataResults', text);
                log('Sample data creation completed', 'success');
            } catch (error) {
                log('Sample data creation failed: ' + error.message, 'error');
                logTo('dataResults', 'Error: ' + error.message, 'error');
            }
        }

        async function viewAllData() {
            log('Viewing all messaging data...', 'info');
            try {
                const response = await fetch('view_messaging_data.php');
                const text = await response.text();
                logTo('dataResults', text);
                log('Data viewing completed', 'success');
            } catch (error) {
                log('Data viewing failed: ' + error.message, 'error');
                logTo('dataResults', 'Error: ' + error.message, 'error');
            }
        }

        function testFrontendFunctions() {
            log('Testing frontend JavaScript functions...', 'info');
            
            // Test if we can access messaging functions
            const tests = [
                {
                    name: 'formatTimeAgo function',
                    test: () => typeof formatTimeAgo === 'function',
                    expected: true
                },
                {
                    name: 'DOM elements exist',
                    test: () => document.getElementById('searchConversations') !== null,
                    expected: true
                },
                {
                    name: 'Fetch API available',
                    test: () => typeof fetch === 'function',
                    expected: true
                },
                {
                    name: 'JSON parse available',
                    test: () => typeof JSON.parse === 'function',
                    expected: true
                }
            ];
            
            let results = '<strong>Frontend Tests:</strong><br>';
            tests.forEach(test => {
                try {
                    const result = test.test();
                    if (result === test.expected) {
                        results += `✓ ${test.name}: PASS<br>`;
                        log(`Frontend test passed: ${test.name}`, 'success');
                    } else {
                        results += `❌ ${test.name}: FAIL (got ${result}, expected ${test.expected})<br>`;
                        log(`Frontend test failed: ${test.name}`, 'error');
                    }
                } catch (error) {
                    results += `❌ ${test.name}: ERROR (${error.message})<br>`;
                    log(`Frontend test error: ${test.name} - ${error.message}`, 'error');
                }
            });
            
            logTo('frontendResults', results);
        }

        // Auto-run basic tests on page load
        window.onload = function() {
            log('Debug tool loaded - Click buttons to run tests', 'info');
        };
    </script>
</body>
</html>
