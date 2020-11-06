function riseCallback(btn) {

}

function doubtCallback(btn) {

}

function cardChangeCallback(btn) {

}

function starterCallback(btn) {

}

function kickCallback(btn) {

}


function addPlayerCard(player) {
    // Construct card HTML
    let card = `                    
        <div class="card mb-3 shadow-sm" id="playerCart${player.ID}">
            <div class="card-header h5">
                ${player.name}
                <cite class="text-secondary" id="playerNCards${player.ID}"> - ${player.nCards} cards</cite>
            </div>
            <div class="card-body">
                <h1 class="mb-0 big-text" id="playerCards${player.ID}">&#127183;</h1>
                <div class="btn-group btn-block btn-group-sm mt-3 d-none" role="group" id="playerBtn${player.ID}">
                    <button type="button" class="btn btn-outline-primary" name="${player.ID}" id="riseBtn${player.ID}">Rise</button>
                    <button type="button" class="btn btn-outline-warning" name="${player.ID}" id="doubtBtn${player.ID}">Doubt</button>
                </div>
                <div class="btn-group btn-block btn-group-sm mt-3 d-none" role="group" id="dealerBtn${player.ID}">
                    <button type="button" class="btn btn-outline-secondary" name="${player.ID}UP" id="cardUpBtn${player.ID}">Card Up</button>
                    <button type="button" class="btn btn-outline-secondary" name="${player.ID}DOWN" id="cardDownBtn${player.ID}">Card Down</button>
                    <button type="button" class="btn btn-success" name="${player.ID}" id="starterBtn${player.ID}">Starter</button>
                    <button type="button" class="btn btn-danger" name="${player.ID}" id="kickBtn${player.ID}">Kick</button>
                </div>
            </div>
        </div>
    `

    // Where to add card
    const rowDiv = document.getElementById('rowDiv')
    let childDiv = document.createElement('div')
    childDiv.classList.add('col-md-6')
    rowDiv.appendChild(childDiv)

    // Append
    childDiv.innerHTML = card

    // Need to wait tha element exists before
    let handle = setInterval(() => {
        try {
            // Adding event listeners
            if (player.itsMe) {
                document.getElementById(`riseBtn${player.ID}`).addEventListener('click', riseCallback)
                document.getElementById(`doubtBtn${player.ID}`).addEventListener('click', doubtCallback)
            }

            if (player.itsMe && player.isDealer) {
                document.getElementById(`cardUpBtn${player.ID}`).addEventListener('click', cardChangeCallback)
                document.getElementById(`cardDownBtn${player.ID}`).addEventListener('click', cardChangeCallback)
                document.getElementById(`starterBtn${player.ID}`).addEventListener('click', starterCallback)
                document.getElementById(`kickBtn${player.ID}`).addEventListener('click', kickCallback)
            }

            clearInterval(handle)
        } catch (e) {
            console.log('re')
        }
    }, 100)
}

function updatePlayerCard(player, cardRef, gameOn) {
    // Update current player turn
    if (player.hisTurn) {
        cardRef.classList.remove('border-danger')
        cardRef.classList.add('border-success')
    } else {
        cardRef.classList.remove('border-success')
    }

    // Il player has no cards
    if (player.nCards === 0) {
        cardRef.classList.remove('border-success')
        cardRef.classList.add('border-danger')
    }

    // Number of cards count
    document.getElementById(`playerNCards${player.ID}`).innerText = ` - ${player.nCards} cards`

    // Show buttons if its turn
    const playerBtn = document.getElementById(`playerBtn${player.ID}`)
    if (player.hisTurn && player.itsMe && gameOn) {
        playerBtn.classList.remove('d-none')
    } else { // Hide
        playerBtn.classList.add('d-none')
    }

    // Update cards
    const playerCards = document.getElementById(`playerCards${player.ID}`)
    if (player.cards == null) { // Show covered
        playerCards.innerHTML = '&#x1F0A0;'.repeat(player.nCards)
    } else { // Show actual cards
        playerCards.innerHTML = player.cards.join('')
    }
}