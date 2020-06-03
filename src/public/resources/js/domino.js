$(document).ready(function() {

$("#start-game-buttton").click(
	function () {
		
		var numOfPlayers = $("#number-player-input").val();
		
		// Check Input
		if (numOfPlayers < 2 || numOfPlayers > 4) {
			
			// Show error message
			$("#number-player-input-error").show();
			$("#game-result").empty();
			
		} else {
			// Process game and get result
			$("#number-player-input-error").hide();
			
			var ajaxPost = $.post("app/domino.php", {'numPlayers': numOfPlayers });
			
			ajaxPost.done(function( data ) {
				data = $.parseJSON( data );
				
				// Inital setup
				$("#progress").empty();
				$.each(data.progress.start.hands, function (index, value){
					index++;
					$("#progress").append( "<p>Player #" + index + " starting hand: <span style='font-size: 3rem'>" + value.join('')  + "</span></p>");
				});
				$("#progress").append("<h4>First tile on the table</h4><p><span style='font-size: 3rem'>"+data.progress.start.table+"</span></p>");
				
				// Progress
				$("#progress").append("<h4>Progress</h4>");
				$.each(data.progress.game, function (index, value){
					index++;
					$("#progress").append( "<p>"+value+"</p>");
				});

				// Results
		    $("#result").empty().append( data.winner );
		    $("#played-tiles").empty().append( data.playedTiles );
		  });
			
		}
	}	
);

});