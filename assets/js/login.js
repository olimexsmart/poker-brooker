document.addEventListener('DOMContentLoaded', function() {
    const newRoomBtn = document.getElementById('newRoom')
    const enterRoomBtn = document.getElementById('enterRoom')
    const roomCodeIn = document.getElementById('roomCode')
    const playerNameIn = document.getElementById('playerName')
    const newRoomCodeAl = document.getElementById('newRoomCode')
    const errEnterAl = document.getElementById('errEnter')
    const nStartCardsIn = document.getElementById('nStartCards')

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
        let playerName = playerNameIn.value
        let roomCode = roomCodeIn.value.toUpperCase()

        fetch(`API/enterRoom.php?playerName=${playerName}&roomCode=${roomCode}`)
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        localStorage.setItem('playerID', text)
                        localStorage.setItem('roomCode', roomCode)
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