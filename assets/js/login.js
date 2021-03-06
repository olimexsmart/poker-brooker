document.addEventListener('DOMContentLoaded', function() {
    const newRoomBtn = document.getElementById('newRoom')
    const enterRoomBtn = document.getElementById('enterRoom')
    const roomCodeIn = document.getElementById('roomCode')
    const playerNameIn = document.getElementById('playerName')
    const newRoomCodeAl = document.getElementById('newRoomCode')
    const errEnterAl = document.getElementById('errEnter')
    // const nStartCardsIn = document.getElementById('nStartCards')
    const spectatorCk = document.getElementById('spectator')

    function pressEnterToClick(event) {
        if (event.keyCode == 13) {
            const e = new Event('click')
            enterRoomBtn.dispatchEvent(e)
        }
    }

    roomCodeIn.addEventListener('keydown', pressEnterToClick)
    playerNameIn.addEventListener('keydown', pressEnterToClick)

    newRoomBtn.addEventListener('click', () => {
        let nStCs = 2

        fetch('API/createRoom.php?nStartCards=' + nStCs)
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        newRoomCodeAl.innerText = 'Room created! Code: ' + text
                        newRoomCodeAl.classList.remove('d-none')
                        newRoomCodeAl.classList.remove('alert-danger')
                        newRoomCodeAl.classList.add('alert-success')

                        roomCodeIn.value = text
                    })
                } else {
                    response.text().then(text => {
                        newRoomCodeAl.innerText = 'Error: ' + text
                        newRoomCodeAl.classList.remove('d-none')
                        newRoomCodeAl.classList.remove('alert-success')
                        newRoomCodeAl.classList.add('alert-danger')
                    })
                }
            })
    })

    enterRoomBtn.addEventListener('click', () => {
        // Just alfanumeric entries
        let playerName = playerNameIn.value.replace(/[^a-z0-9]/gi,'')

        // Avoid no-name players
        if (playerName.length == 0) {
            errEnterAl.innerText = "Inserire nome giocatore"
            errEnterAl.classList.remove('d-none')
            errEnterAl.classList.remove('alert-success')
            errEnterAl.classList.add('alert-danger')
            return
        } else if (playerName.length > 19) { // Avoid name to long
            playerName = playerName.substring(0, 19);
        }

        let roomCode = roomCodeIn.value.toUpperCase()
        let spectator = spectatorCk.checked ? 1 : 0;

        fetch(`API/enterRoom.php?playerName=${playerName}&roomCode=${roomCode}&spectator=${spectator}`)
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        sessionStorage.setItem('playerID', text)
                        sessionStorage.setItem('roomCode', roomCode)
                        window.location.href = location.pathname + 'game/game.html'
                    })
                } else {
                    response.text().then(text => {
                        errEnterAl.innerText = text
                        errEnterAl.classList.remove('d-none')
                        errEnterAl.classList.remove('alert-success')
                        errEnterAl.classList.add('alert-danger')
                    })
                }
            })
    })

})