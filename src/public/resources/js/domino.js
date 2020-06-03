$(document).ready(function() {


$("#start-game-buttton").click(
	function () {
		var numOfPlayers = $("#number-player-input").val();
		if (numOfPlayers < 2 || numOfPlayers > 4) {
			$("#input-alert").show();
			$( "#result" ).empty();
		} else {
			$("#input-alert").hide();
			
			var ajaxPost = $.post("app/domino.php", {'numPlayers': numOfPlayers });
			
			ajaxPost.done(function( data ) {
				data = $.parseJSON( data );
				
				// Inital setup
				$( "#progress" ).empty();
				$.each(data.progress.start.hands, function (index, value){
					index++;
					$( "#progress" ).append( "<p>Player #" + index + " starting hand: <span style='font-size: 3rem'>" + value.join('')  + "</span></p>");
				});
				$( "#progress" ).append("<h2>First tile on the table</h2><p><span style='font-size: 3rem'>"+data.progress.start.table+"</span></p>");
				$( "#progress" ).append("<h1>Progress</h1>");
				$.each(data.progress.game, function (index, value){
					index++;
					$( "#progress" ).append( "<p>"+value+"</p>");
				});

		    $( "#result" ).empty().append( data.winner );
		    $( "#played-tiles" ).empty().append( data.playedTiles );
		  });
			
		}
	}	
);




});