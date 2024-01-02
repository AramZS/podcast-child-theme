function htmxJackPlayers() {
	document.querySelectorAll("audio").forEach((e) => {
		console.log(e);
		e.addEventListener("canplaythrough", (e) => {
			// The duration variable now holds the duration (in seconds) of the audio clip
		});
		e.addEventListener("play", (e) => {
			window["stable-container"].innerHTML = "";
			window["stable-container"].append(e.target);
			window["stable-container"].style.display = "block";
			// The duration variable now holds the duration (in seconds) of the audio clip
		});
	});
}

function htmxSwapCallback() {
	console.log("Custom element has seen an htmx swap.");
	htmxJackPlayers();
}

document.addEventListener("DOMContentLoaded", function (evt) {
	htmxJackPlayers();

	document.body.addEventListener("htmx:afterOnLoad", function (evt) {
		htmxSwapCallback();
	});
});
