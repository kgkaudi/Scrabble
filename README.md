# Scrabble

A typical scrabble game, that can be executed locally by putting docker-compose up

File style.css contains all the styling contained in the page.

File letters.js contains the letters with their respective points and how many are there from the beginning.

File script.js contains the JavaScript logic in the code.
First we check if the value remainingTiles (Which contains the number of remaining tiles after each submit) exists, and if so, we update that number to the remaining one. 
Then we have a logic for the winner of the game, based on the score of each user until the bag runs out of new letters.
Next, we have the code to drag our tiles to the board.
Next, we have the code to update each cell, based on the letter we dropped. This also contains the updatin of remainingTiles value.
The next bit contains the submit logic, which contains also the check of how many letters are dropped, and whether the submit is allowed or not.
And finaly, we have a function for messages, which can get a message, a message colour and for how long will it last.

File index.php contains the PHP login in the code.
First we get all the necessary details from the letters.json.
There is a function to draw random tiles and another function that draws letters from the remaining ones.
The next function is responsible for the scoring system.
Afterwards, that piece of code initialises the players and the board.
We have logic for the submit and how the points and the refill is used.
And finally we have the html part of the whole operation.