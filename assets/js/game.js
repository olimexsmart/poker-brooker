document.addEventListener('DOMContentLoaded', function() {
    const roomCodeH3 = document.getElementById('roomCodeH3')
    const exitBtn = document.getElementById('exitBtn')
    const gameInfoP = document.getElementById('gameInfoP')
    const adminBtn = document.getElementById('adminBtn')
    const errAlertAl = document.getElementById('errAlert')

    // Set room code label
    roomCodeH3.innerText = localStorage.getItem('roomCode')

    // Exit button
    exitBtn.addEventListener('click', () => {
        fetch('../API/exitRoom.php?playerID=' + localStorage.getItem('playerID'))
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        localStorage.removeItem('playerID')
                        window.history.back();
                    })
                } else {
                    response.text().then(text => {
                        errAlertAl.innerText = text
                        errAlertAl.classList.remove('d-none')
                    })
                }
            })
    })

    adminBtn.addEventListener('click', () => {
        // Depends on current button state

        // Request card distribution - game on
        // Request card reveal - game off
        // Hide - Show controls on player cards
    })

    // Define function callbacks for buttons in cards

    setInterval(() => {
        fetch('../API/loadPlayers.php?playerID=' + localStorage.getItem('playerID'))
            .then(response => {
                if (response.ok) {
                    response.json().then(text => {
                        let totCards = 0;
                        for (let c = 0; c < text.players.length; c++) {
                            const player = text.players[c];

                            totCards += player.nCards;

                            // Admin button
                            if (player.isDealer && player.itsMe) {
                                adminBtn.classList.remove('d-none')
                            }

                            // Switch on-off admin buttons on players

                            // If it's me, show my buttons

                            // Add card if not already present

                            // Update card if already present

                            // Header number of cards

                            // If cards array is populated, show cards
                            // Else show turned cards

                            // Remove card if player not present anymore
                        }

                        // Updating game info
                        gameInfoP.innerText = `Cards: ${totCards}
                        Players in game: ${text.players.length}
                        Game: ${text.gameOn ? 'ON' : 'OFF'}`
                    })
                } else {
                    response.text().then(text => {
                        errAlertAl.innerText = text
                        errAlertAl.classList.remove('d-none')
                    })
                }
            })
    }, 1000)
})