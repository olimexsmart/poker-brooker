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
        if (adminBtn.classList.contains('btn-danger')) {
            // Request card reveal - game off
            fetch('../API/doubt.php?playerID=' + localStorage.getItem('playerID'))
                .then(response => {
                    if (!response.ok) {
                        response.text().then(text => {
                            errAlertAl.innerText = text
                            errAlertAl.classList.remove('d-none')
                        })
                    }
                })
        } else {
            // Request card distribution - game on
            fetch('../API/distributeCards.php?dealerID=' + localStorage.getItem('playerID'))
                .then(response => {
                    if (!response.ok) {
                        response.text().then(text => {
                            errAlertAl.innerText = text
                            errAlertAl.classList.remove('d-none')
                        })
                    }
                })
        }
    })

    // Define function callbacks for buttons in cards

    setInterval(() => {
        fetch('../API/loadPlayers.php?playerID=' + localStorage.getItem('playerID'))
            .then(response => {
                if (response.ok) {
                    response.json().then(text => {
                        let totCards = 0;
                        let allCards = Array.from(document.querySelectorAll('*[id^="playerCart"]'))
                        for (let c = 0; c < text.players.length; c++) {
                            const player = text.players[c];

                            totCards += player.nCards;

                            // Admin button
                            if (player.isDealer && player.itsMe) {
                                adminBtn.classList.remove('d-none')
                                    // Toggle button function
                                if (text.gameOn) {
                                    adminBtn.classList.remove('btn-success')
                                    adminBtn.classList.add('btn-danger')
                                    adminBtn.innerText = 'Stop round'
                                } else {
                                    adminBtn.classList.remove('btn-danger')
                                    adminBtn.classList.add('btn-success')
                                    adminBtn.innerText = 'New round'
                                }

                                // Switch on-off admin buttons on players
                                let dealerBtn = document.querySelectorAll('*[id^="dealerBtn"]');
                                if (text.gameOn) {
                                    dealerBtn.forEach(e => {
                                        e.classList.add('d-none')
                                    })
                                } else {
                                    dealerBtn.forEach(e => {
                                        e.classList.remove('d-none')
                                    })
                                }
                            }

                            // Add card if not already present
                            const cardRef = document.getElementById(`playerCart${player.ID}`)
                            if (!cardRef) {
                                addPlayerCard(player)
                            } else { // Update card if already present
                                updatePlayerCard(player, cardRef, text.gameOn)

                                // Remove from array of childs
                                let index = allCards.indexOf(cardRef)
                                if (index > -1) {
                                    allCards.splice(index, 1);
                                }
                            }
                        }

                        // Remove elements not present in DB
                        for (let i = 0; i < allCards.length; i++) {
                            allCards[i].parentNode.remove()
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