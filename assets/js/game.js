document.addEventListener('DOMContentLoaded', function() {
    const roomCodeH3 = document.getElementById('roomCodeH3')
    const exitBtn = document.getElementById('exitBtn')
    const gameInfoP = document.getElementById('gameInfoP')
    const adminBtn = document.getElementById('adminBtn')
    const errAlertAl = document.getElementById('errAlert')

    // Set room code label
    roomCodeH3.innerText = sessionStorage.getItem('roomCode')

    // Exit button
    exitBtn.addEventListener('click', () => {
        fetch('../API/exitRoom.php?playerID=' + sessionStorage.getItem('playerID'))
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        sessionStorage.removeItem('playerID')
                        window.location.href = location.origin + '/pokerpolacco/'
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
            fetch('../API/doubt.php?playerID=' + sessionStorage.getItem('playerID'))
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
            fetch('../API/distributeCards.php?dealerID=' + sessionStorage.getItem('playerID'))
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

    // Periodically update player cards
    setInterval(() => {
        fetch('../API/loadPlayers.php?playerID=' + sessionStorage.getItem('playerID'))
            .then(response => {
                if (response.ok) {
                    response.json().then(text => {
                        let totCards = 0;
                        let allCards = Array.from(document.querySelectorAll('*[id^="playerCart"]'))
                        let adminName = ''
                        for (let c = 0; c < text.players.length; c++) {
                            const player = text.players[c];

                            totCards += player.nCards;

                            // Admin buttons
                            let dealerBtn = document.querySelectorAll('*[id^="dealerBtn"]');
                            if (player.isDealer && player.itsMe) {
                                adminBtn.classList.remove('d-none')
                                    // Toggle button function
                                if (text.gameOn) {
                                    adminBtn.classList.remove('btn-success')
                                    adminBtn.classList.add('btn-danger')
                                    adminBtn.innerText = 'Annulla Mano'
                                } else {
                                    adminBtn.classList.remove('btn-danger')
                                    adminBtn.classList.add('btn-success')
                                    adminBtn.innerText = 'Distribuisci Carte'
                                }

                                // Switch on-off admin buttons on players
                                if (text.gameOn) {
                                    dealerBtn.forEach(e => {
                                        e.classList.add('d-none')
                                    })
                                } else {
                                    dealerBtn.forEach(e => {
                                        e.classList.remove('d-none')
                                    })
                                }
                            } else if (!player.isDealer && player.itsMe) { // Just to be sure
                                adminBtn.classList.add('d-none')
                                dealerBtn.forEach(e => {
                                    e.classList.add('d-none')
                                })
                            }

                            // Save admin admin
                            if (player.isDealer)
                                adminName = player.name

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
                        gameInfoP.innerHTML = `Carte: ${totCards} &mdash; 
                        Giocatori: ${text.players.length} <br>
                        Mazziere: ${adminName} &mdash;
                        Round: <b>${text.gameOn ? '<span style="color: greenyellow;">In Corso &#x2691;</span>' 
                                                : '<span style="color: darkred;">In attesa &#x2612;</span>'}</b>`
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