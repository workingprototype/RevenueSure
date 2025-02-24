<?php
require_once ROOT_PATH . 'helper/core.php';
redirectIfUnauthorized(true);

$error = '';
$success = '';

$drawing_board_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch drawing board details
$stmt = $conn->prepare("SELECT * FROM drawing_boards WHERE id = :id");
$stmt->bindParam(':id', $drawing_board_id);
$stmt->execute();
$drawing_board = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$drawing_board) {
    header("Location: " . BASE_URL . "drawings/manage");
    exit();
}

// Fetch collaborators
$stmt = $conn->prepare("SELECT users.id, users.username FROM drawing_board_collaborators LEFT JOIN users ON drawing_board_collaborators.user_id = users.id WHERE drawing_board_id = :drawing_board_id");
$stmt->bindParam(':drawing_board_id', $drawing_board_id);
$stmt->execute();
$collaborators = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users for collaborator selection (except the current user)
$allUsers = getUserList($conn, $_SESSION['user_id']);

// Check if the user has permission to view the drawing board
$isCollaboratorStmt = $conn->prepare("SELECT 1 FROM drawing_board_collaborators WHERE drawing_board_id = :drawing_board_id AND user_id = :user_id");
$isCollaboratorStmt->bindParam(':drawing_board_id', $drawing_board_id, PDO::PARAM_INT);
$isCollaboratorStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$isCollaboratorStmt->execute();
$isCollaborator = $isCollaboratorStmt->fetch();

if ($drawing_board['created_by'] != $_SESSION['user_id'] && !$isCollaborator) {
    echo "You do not have permission to edit this drawing board.";
    exit();
}

// Get current user details for TogetherJS
$currentUserStmt = $conn->prepare("SELECT username, email, profile_picture FROM users WHERE id = :user_id");
$currentUserStmt->bindParam(':user_id', $_SESSION['user_id']);
$currentUserStmt->execute();
$currentUser = $currentUserStmt->fetch(PDO::FETCH_ASSOC);

// Function to convert image to data URL
function getImageDataUrl($imagePath) {
    if (file_exists($imagePath)) {
        $type = pathinfo($imagePath, PATHINFO_EXTENSION);
        $data = file_get_contents($imagePath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    // If file doesn't exist, return path to default image
    return 'data:image/png;base64,' . base64_encode(file_get_contents('public/uploads/profile/default_profile.png'));
}

// Get image data URL
$avatarUrl = getImageDataUrl($currentUser['profile_picture']);

?>

    <!-- React Dependencies -->
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>

    <!-- Excalidraw -->
    <script src="https://unpkg.com/@excalidraw/excalidraw/dist/excalidraw.production.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@excalidraw/excalidraw/dist/excalidraw.min.css"/>

    <!-- TogetherJS -->
    <script src="https://insanelyelegant.com/WriteTogether/togetherjs.js"></script>
    <style>
        /* Responsive design for the canvas */
#excalidraw-container {
    position: relative;
    width: 100%;
    height: 80vh;
    min-height: 600px; /* Or some appropriate minimum value */
    border: 1px solid #ccc;
}

        .size-control, .fullscreen-control {
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #ccc;
            padding: 5px;
            cursor: pointer;
            z-index: 10;
        }

        .size-control {
            top: 10px;
            left: 10px;
        }

        .fullscreen-control {
            top: 10px;
            right: 10px;
        }

        @media (max-width: 768px) {
            #excalidraw-container {
                height: 70vh; /* Adjust the height for smaller screens */
            }
        }
    </style>
<div class="container mx-auto p-6 fade-in">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Drawing Board: <?php echo htmlspecialchars($drawing_board['title']); ?></h1>

    <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <div id="excalidraw-container">
            <div class="size-control" id="increase-size">➕</div>
            <div class="fullscreen-control" id="fullscreen-toggle">🖥</div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Collaborators</h2>

        <form id="add_collaborator_form" method="POST" action="actions/add_collaborator.php">
            <input type="hidden" name="drawing_board_id" value="<?php echo $drawing_board_id; ?>">
            <div class="mb-4">
                <label for="user_id" class="block text-gray-700">Add Collaborator:</label>
                <select name="user_id" id="user_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">Select a user</option>
                    <?php foreach ($allUsers as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300">Add</button>
        </form>

        <?php if ($collaborators): ?>
            <ul>
                <?php foreach ($collaborators as $collaborator): ?>
                    <li class="mb-2"><?php echo htmlspecialchars($collaborator['username']); ?>
                        <form method='POST' action='actions/drop_collaborator.php'>
                            <input type='hidden' name='drawing_board_id' value='<?php echo $drawing_board_id ?>'>
                            <input type='hidden' name='user_id' value='<?php echo $collaborator['id'] ?>'>
                            <button type='submit' class='bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300'>Drop</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">No collaborators for this drawing board.</p>
        <?php endif; ?>
    </div>

    <div class="mt-6 flex justify-center gap-4">
        <a href="<?php echo BASE_URL; ?>drawings/manage" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300">Back to Drawing Boards</a>
        <button id="startCollaboration" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Start Collaborating</button>
    </div>
 <div class="flex justify-center mt-4">
        <button id="manualSaveButton" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 mr-2">Save</button>
        <button id="refreshBoardButton" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">Refresh Board</button>
    </div>

    <!-- Autosaved Toast -->
    <div id="autosavedToast" class="fixed bottom-4 right-4 bg-green-500 text-white p-3 rounded-lg shadow-md transition duration-300 opacity-0" style="min-width: 200px;">
        Autosaved: <span id="lastSavedTime"></span>
    </div>
</div>

<script>
    // TogetherJS Configuration
    TogetherJSConfig_hubBase = "https://togetherjs-hub.glitch.me/";
    TogetherJSConfig_getUserName = function () {
        return <?php echo json_encode($currentUser['username']); ?>;
    };
    TogetherJSConfig_getUserAvatar = function () {
        return <?php echo json_encode($avatarUrl); ?>;
    };
    TogetherJSConfig_suppressJoinConfirmation = true;
    TogetherJSConfig_suppressInvite = true;


    document.addEventListener('DOMContentLoaded', function() {

        const container = document.getElementById('excalidraw-container');
    if (container) {
        container.style.height = '80vh'; // Or '600px' or whatever is appropriate
    }

        // Auto-start collaboration if URL contains the collaboration parameter
        const urlParams = new URLSearchParams(window.location.search);
        const startCollab = urlParams.get('collaborate');

        if (startCollab === 'true') {
            // Small delay to ensure TogetherJS is fully loaded
            setTimeout(() => {
                if (!TogetherJS.running) {
                    TogetherJS();
                }
            }, 1000);
        }

        // Manual collaboration button
        document.getElementById('startCollaboration').addEventListener('click', function() {
            if (!TogetherJS.running) {
                TogetherJS();
            }
        });

        const App = () => {
            const excalidrawRef = React.useRef(null);
            const [elements, setElements] = React.useState([]);
            const [appState, setAppState] = React.useState({});
            const [isMounted, setIsMounted] = React.useState(true); // Track mount state
            const [lastSaved, setLastSaved] = React.useState(new Date());
            const options = { aiEnabled: true };
            const appRef = React.useRef(null);
            const previousData = React.useRef(null);
             const lastReceivedTogetherJSData = React.useRef(null); // Track last received data from TogetherJS

             // Function to show the "Autosaved" toast
             const showToast = React.useCallback((time) => {
                const toast = document.getElementById('autosavedToast');
                const lastSavedTime = document.getElementById('lastSavedTime');
                lastSavedTime.textContent = time.toLocaleTimeString();
                toast.classList.add('opacity-100');
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                }, 3000); // Hide after 3 seconds
            }, []);

             // Function to perform the save operation
              const performSave = React.useCallback(() => {
                console.log("performSave called!");
            return new Promise((resolve, reject) => {
                console.log("Performing save operation...");

                // Deep comparison
                const currentData = JSON.stringify({ elements: elements, appState: appState });
                const previousDataValue = previousData.current ? JSON.stringify(previousData.current) : null;

                if (currentData === previousDataValue) {
                    console.log("No changes detected, skipping save.");
                    resolve(); // Resolve the promise
                    return;
                }

                const saveData = {
                    elements: elements,
                    appState: appState
                };

                fetch('actions/save.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'drawing_board_id=<?php echo $drawing_board_id; ?>&elements=' +
                        encodeURIComponent(JSON.stringify(saveData))
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            console.log('Save successful');
                            const now = new Date();
                            setLastSaved(now);
                            showToast(now);

                            // Update previous data
                            previousData.current = { elements: elements, appState: appState };
                            resolve(); // Resolve the promise
                        } else {
                            console.error('Save failed:', result.message);
                            reject(result.message); // Reject the promise
                        }
                    })
                    .catch(error => {
                        console.error('Error saving:', error);
                        reject(error); // Reject the promise
                    });
            });
        }, [elements, appState, showToast]);

             // Manual refresh function
             const manualRefreshBoard = React.useCallback(() => {
                console.log("Manual refresh triggered");
                fetch(`actions/get_drawing_data.php?drawing_board_id=<?php echo $drawing_board_id; ?>`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            console.log("Successfully fetched drawing data for manual refresh.");
                            setElements(data.elements);
                            setAppState(data.appState);

                             if (excalidrawRef.current?.excalidrawAPI) {
                                excalidrawRef.current.excalidrawAPI.updateScene({
                                    elements: data.elements,
                                    appState: data.appState,
                                });
                            } else {
                                console.warn("excalidrawAPI not yet available during manual refresh.");
                            }
                        } else {
                            console.error("Error fetching drawing data for manual refresh:", data.message);
                        }
                    })
                    .catch(error => console.error("Error fetching drawing data:", error));
            }, [setElements, setAppState, excalidrawRef]);

             // Function to trigger manual save and update the last saved time
const manualSave = React.useCallback(() => {
        console.log("Manual save triggered");
          performSave().then(() => {
                // Show manual save message after successful save
                appRef.current.showToast(new Date(), 'Manual save successful!');
            });
    }, [performSave]);

              const onChangeHandler = React.useCallback((newElements, newAppState) => {
                if (!isMounted) {
                    return;
                }

                newAppState.collaborators = Array.isArray(newAppState.collaborators) ? newAppState.collaborators : [];

                // Check if the update is coming from TogetherJS and compare data
                if (typeof TogetherJS !== 'undefined' && TogetherJS.running) {
                    const incomingData = JSON.stringify({ elements: newElements, appState: newAppState });
                    const lastReceivedData = lastReceivedTogetherJSData.current;

                    if (lastReceivedData === incomingData) {
                        console.log("Data from TogetherJS is identical, skipping update.");
                        return;
                    }
                }

                setElements(newElements);
                setAppState(newAppState);

                // AFTER updating state, send the update to TogetherJS
                if (typeof TogetherJS !== 'undefined' && TogetherJS.running) {
                    TogetherJS.send({
                        type: "app.excalidraw.update",
                        elements: newElements,
                        appState: {
                            ...newAppState,
                            collaborators: newAppState.collaborators
                        }
                    });

                     // Update the last received data (AFTER sending, so it's the *local* change)
                    lastReceivedTogetherJSData.current = JSON.stringify({ elements: newElements, appState: newAppState });
                }

            }, [isMounted, setElements, setAppState]); // Ensure setElements and setAppState are included

            // Parse the initial data
            let initialData = {elements: [], appState: {}};
            try {
                const drawingBoardElements = '<?php echo $drawing_board['elements'] ? $drawing_board['elements'] : '{}'; ?>';
                const parsedData = JSON.parse(drawingBoardElements);

                if (typeof parsedData === 'object' && parsedData !== null) {
                    initialData = {
                        elements: Array.isArray(parsedData.elements) ? parsedData.elements : [],
                        appState: (typeof parsedData.appState === 'object' && parsedData.appState !== null) ? parsedData.appState : {}
                    };
                } else {
                    console.warn('Parsed data is not an object, using default initial data.');
                }
            } catch (e) {
                console.error('Error parsing initial data:', e);
                initialData = {elements: [], appState: {}};
            }

            // Ensure appState has required properties, INCLUDING collaborators
            initialData.appState = {
                viewBackgroundColor: initialData.appState?.viewBackgroundColor || "#ffffff",
                currentItemFontFamily: initialData.appState?.currentItemFontFamily || 1,
                collaborators: Array.isArray(initialData.appState?.collaborators) ? initialData.appState.collaborators : [], // IMPORTANT: Initialize collaborators as an array
                ...initialData.appState
            };

            // Initialize the state variables
            React.useEffect(() => {
                setElements(initialData.elements);
                setAppState(initialData.appState);
            }, []);

              // Auto-save effect
           React.useEffect(() => {
                const autoSaveInterval = setInterval(() => {
                    performSave();
                }, 5000);  // Auto save every 5 seconds

                return () => clearInterval(autoSaveInterval);
            }, [performSave]);

            React.useImperativeHandle(appRef, () => ({
                manualSave: manualSave,
                manualRefreshBoard: manualRefreshBoard,
                showToast: (time, message = 'Autosaved') => { // Added message parameter
                    const toast = document.getElementById('autosavedToast');
                    const lastSavedTime = document.getElementById('lastSavedTime');
                    lastSavedTime.textContent = time.toLocaleTimeString();
                    toast.textContent = message + ': ' + lastSavedTime.textContent; // Update the toast message
                    toast.classList.add('opacity-100');
                    setTimeout(() => {
                        toast.classList.remove('opacity-100');
                    }, 3000); // Hide after 3 seconds
                },
             }));

            return React.createElement(ExcalidrawLib.Excalidraw, {
                ref: excalidrawRef,
                initialData: { elements: elements, appState: appState },
                onChange: onChangeHandler,
                options: options
            });
        };

       setTimeout(() => {
        // Your Excalidraw initialization code here (the `App` component rendering)
        const container = document.getElementById("excalidraw-container");
        const root = ReactDOM.createRoot(container);
        let appRef; // Declare appRef outside of App

        const AppWrapper = React.forwardRef((props, ref) => {
            appRef = ref; // Assign the ref to appRef
            return React.createElement(App, props);
        });

        root.render(React.createElement(AppWrapper, { ref: (ref) => (appRef = ref) }));
         // Attach event listeners for manual save and refresh
        document.getElementById('manualSaveButton').addEventListener('click', () => {
            if (appRef && appRef.manualSave) {
                appRef.manualSave();
            }
        });

        document.getElementById('refreshBoardButton').addEventListener('click', () => {
            if (appRef && appRef.manualRefreshBoard) {
                appRef.manualRefreshBoard();
            }
        });
    }, 100); // Delay by 100 milliseconds (adjust as needed)
    });
</script>