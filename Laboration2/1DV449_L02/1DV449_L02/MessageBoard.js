var MessageBoard = {

    messages: [],
    textField: null,
    messageArea: null,
    largestSerial: 0,
    prevNumOfMessages: 0,

    init:function(e)
    {

		    MessageBoard.textField = document.getElementById("inputText");
		    MessageBoard.nameField = document.getElementById("inputName");
            MessageBoard.messageArea = document.getElementById("messagearea");

            // Add eventhandlers
            document.getElementById("inputText").onfocus = function(e){ this.className = "focus"; }
            document.getElementById("inputText").onblur = function(e){ this.className = "blur" }
            document.getElementById("buttonSend").onclick = function(e) {MessageBoard.sendMessage(); return false;}
            document.getElementById("buttonLogout").onclick = function(e) {MessageBoard.logout(); return false;}

            MessageBoard.textField.onkeypress = function(e){
                                                    if(!e) var e = window.event;

                                                    if(e.keyCode == 13 && !e.shiftKey){
                                                        MessageBoard.sendMessage();

                                                        return false;
                                                    }
                                                }

    },
    getMessages:function() {
        console.log("INNE");
        $.ajax({
            type: "GET",
            url: "functions.php",
            data: {
                function: "getMessages", serial: MessageBoard.largestSerial
            },
            timeout: "6000",
            cache: "false"

        }).done(function(data) { // called when the AJAX call is ready
            alert("Success!");
            data = JSON.parse(data);

            for (var mess in data) {
                console.log("for");
                var obj = data[mess];                       // Gör varje element i arrayen av returnerat data till ett objekt.
                var text = obj.name + " said:\n" + obj.message;   // Texten som ska visas, namn + text läggs i strängen text
                var mess = new Message(text, obj.timestamp); //new Message(text, new Date());
                console.log("text" + text);
                var messageID = MessageBoard.messages.push(mess) - 1;
                MessageBoard.renderMessage(messageID);      // Skriv ut meddelandet i webbläsarfönstret.
                console.log(text + " messID: " + messageID);
            }
            console.log(MessageBoard.largestSerial + " " + MessageBoard.messages.length);
            MessageBoard.largestSerial = obj.serial; // Sätt variabeln för det högsta id:t

            document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length + MessageBoard.prevNumOfMessages; // Hämtar antal element som finns i arrayen för att skriva ut det totala antalet meddelanden som finns.
            // Här behöver koden fixas för att lägga till det nya antalet meddelandet till det tidigare antalet
            MessageBoard.prevNumOfMessages = MessageBoard.messages.length;
            MessageBoard.messages = [];
            console.log(MessageBoard.largestSerial + " " + MessageBoard.messages.length);
        }).fail(function(e) {
            console.log(e);
        }).always(function() {
            console.log("new request")
            MessageBoard.getMessages();
        });
    },
    sendMessage:function(){
        if(MessageBoard.textField.value == "") return;

        // Make call to ajax
        $.ajax({
			type: "POST",
		  	url: "functions.php",
		  	data: {function: "add", name:MessageBoard.nameField.value, message:MessageBoard.textField.value}
        }).done(function(data) {
		  alert("Your message is saved!");
            MessageBoard.nameField.value = "";
            MessageBoard.textField.value = "";
        }).fail(function(e) {
            console.log(e);
		});

    },
    renderMessages: function(){
        // Remove all messages
        MessageBoard.messageArea.innerHTML = "";

        // Renders all messages.
        for(var i=0; i < MessageBoard.messages.length; ++i){
            MessageBoard.renderMessage(i);
        }

        document.getElementById("nrOfMessages").innerHTML = MessageBoard.messages.length;
    },
    renderMessage: function(messageID){
        // Message div
        var div = document.createElement("div");
        div.className = "message";

        // Clock button
        aTag = document.createElement("a");
        aTag.href="#";
        aTag.onclick = function(){
			MessageBoard.showTime(messageID);
			return false;
		}

        var imgClock = document.createElement("img");
        imgClock.src="pic/clock.png";
        imgClock.alt="Show creation time";

        aTag.appendChild(imgClock);
        div.appendChild(aTag);

        // Message text
        var text = document.createElement("p");
        text.innerHTML = MessageBoard.messages[messageID].getHTMLText();
        div.appendChild(text);

        // Time - Should fix on server!
        var spanDate = document.createElement("span");
        spanDate.appendChild(document.createTextNode(MessageBoard.messages[messageID].getDateText()))

        div.appendChild(spanDate);

        var spanClear = document.createElement("span");
        spanClear.className = "clear";

        div.appendChild(spanClear);

        MessageBoard.messageArea.appendChild(div);
    },
    removeMessage: function(messageID){
		if(window.confirm("Vill du verkligen radera meddelandet?")){

			MessageBoard.messages.splice(messageID,1); // Removes the message from the array.

			MessageBoard.renderMessages();
        }
    },
    showTime: function(messageID){

         var time = MessageBoard.messages[messageID].getDate();

         var showTime = "Created "+time.toLocaleDateString()+" at "+time.toLocaleTimeString();

         alert(showTime);
    },
    logout: function() {
        window.location = "index.php";
    }
}

window.onload = MessageBoard.init;