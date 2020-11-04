document.addEventListener('DOMContentLoaded', function() {
    const newRoomBtn = document.getElementById('newRoom')
    const enterRoomBtn = document.getElementById('enterRoom')
    const roomCodeIn = document.getElementById('roomCode')
    const playerNameIn = document.getElementById('playerName')
    const newRoomCodeAl = document.getElementById('newRoomCode')
    const errEnterAl = document.getElementById('errEnter')
    const nStartCardsIn = document.getElementById('nStartCards')


    newRoomBtn.addEventListener('click', () => {
        let nStCs = parseInt(nStartCardsIn.value)
        if (isNaN(nStCs) || nStCs < 1 || nStCs > 7)
            nStCs = 2

        fetch('API/createRoom.php?nStartCards=' + nStCs)
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        newRoomCodeAl.innerText = 'Room created! Code: ' + text
                        newRoomCodeAl.classList.remove('invisible')
                        newRoomCodeAl.classList.remove('alert-danger')
                        newRoomCodeAl.classList.add('alert-success')

                        roomCodeIn.value = text
                    })
                } else {
                    response.text().then(text => {
                        newRoomCodeAl.innerText = 'Error: ' + text
                        newRoomCodeAl.classList.remove('invisible')
                        newRoomCodeAl.classList.remove('alert-success')
                        newRoomCodeAl.classList.add('alert-danger')
                    })
                }
            })
    })

    enterRoomBtn.addEventListener('click', () => {
        let playerName = playerNameIn.value
        let roomCode = roomCodeIn.value

        fetch(`API/enterRoom.php?playerName=${playerName}&roomCode=${roomCode}`)
            .then(response => {
                if (response.ok) {
                    response.text().then(text => {
                        localStorage.setItem('playerID', text)
                        window.location.href = location.pathname + 'game/game.html'
                    })
                } else {
                    response.text().then(text => {
                        errEnterAl.innerText = text
                    })
                }
            })
    })

})