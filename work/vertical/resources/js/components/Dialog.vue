<template>
	<div class="p-modal">
		<dialog open class="p-modal__dialog">
			<div class="p-modal__header">
				<h1 class="p-modal__title">
					<slot name="header"></slot>
				</h1>
			</div>
			<div class="p-modal__body">
				<slot name="body"></slot>
			</div>
			<div class="p-modal__footer">
				<slot name="footer"></slot>
			</div>
		</dialog>
	</div>
</template>
