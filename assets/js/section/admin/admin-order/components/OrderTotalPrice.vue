<template>
    <div class="row mb-1">
        <div class="col-md-1 text-right font-weight-bold">
            Total price:
        </div>
        <div class="col-md-10">
            <span class="font-weight-bold">${{totalPrice}}</span>
        </div>
    </div>
</template>

<script>
	import {mapState} from "vuex";

	export default {
		name: "OrderTotalPrice",
		computed: {
			...mapState('products', ['orderProducts']),
			totalPrice() {
				let totalPrice = 0;
				this.orderProducts.forEach(orderProduct => {
					totalPrice += parseFloat(orderProduct.pricePerOne) * orderProduct.quantity
				});
				return parseFloat(totalPrice);
			}
		}
	}
</script>

<style scoped>

</style>