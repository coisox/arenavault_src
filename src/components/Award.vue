<template>
	<div class="p-absolute page bg-gradient-orange">

		<div class="content">
			<div v-for="item in awards" class="award-container p-relative" @click="edit(item.type)" :key="item.type">
                <img :src="'img/'+item.type+'.png'" class="logo">
                <div class="right">
                    <div class="header">{{item.header}}</div>
                    <div class="text-small">{{item.desc}}</div>
                </div>
            </div>
		</div>

	</div>
</template>

<style scoped>
	.content {
        margin-top: 24px;
        height: calc(100vh - 115px);
    }
    .logo {
        width: 100px;
        position: relative;
        margin-left: 18px;
        z-index: 2;
    }
    .right {
        width: calc(100% - 75px);
        position: absolute;
        top: 50%;
        transform:translateY(-50%);
        right: 20px;
        z-index: 1;
        border-radius: 6px;
        border: 4px solid rgba(255,255,255,.5);
        background-clip: padding-box;
        box-sizing: border-box;
    }
    .header {
        padding: 10px;
        padding-left: 60px;
        background-color: #616161;
        color: white;
    }
    .text-small {
        padding: 10px;
        padding-left: 60px;
        background-color: white;
        font-style: italic;
    }
</style>

<script>
export default {
	data() {
		return {
			awards: [
                {type:'award_king', desc:'Being the longest winning streak (99)', header:'Aizal Manan'},
                {type:'award_brutality', desc:'Overwhelm your opponent 11-0', header:'Aizal Manan'},
                {type:'award_deuce', desc:'For playing the longest deuce (23-20)', header:'Aizal Manan vs Khalil'},
                {type:'award_fearless', desc:'Beat far greater opponent than you', header:'Aizal Manan'},
                {type:'award_weekend', desc:'You love to play on weekend', header:'Aizal Manan'},
                {type:'award_sleeping', desc:'Who has not playing for a long time', header:'Aizal Manan'},
            ]
		};
	},
	props: ['session', 'apiurl'],
	methods: {
		edit(type) {
            if(this.session.ARENA_OWNER) {
                console.log('Is owner, type = '+type)
            }
            else {
                console.log('Cannot edit')
            }
        }
	},
	mounted() {
		// this.getAwards()
	}
};
</script>