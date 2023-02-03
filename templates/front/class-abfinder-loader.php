<div id="preloader">
	<a class="loading-icon icon-rotate"></a>
</div>

<style>
	#preloader {
		display: none;
		z-index: 1000;
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background-color: rgba(255, 255, 255, 0.8);
	}

	.loading-icon {
		z-index: 1001;
		position: fixed;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		background: url(<?php echo ABFINDER_PLUGIN_URL . 'assets/images/refresh.svg' ?>) center no-repeat;
	}

	.icon-rotate {
		-webkit-animation: spin 2s linear infinite;
		-moz-animation: spin 2s linear infinite;
		animation: spin 2s linear infinite;
	}

	@-moz-keyframes spin {
		100% {
			-moz-transform: rotate(360deg);
		}
	}

	@-webkit-keyframes spin {
		100% {
			-webkit-transform: rotate(360deg);
		}
	}

	@keyframes spin {
		100% {
			-webkit-transform: rotate(360deg);
			transform: rotate(360deg);
		}
	}
</style>