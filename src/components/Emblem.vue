<template>
	<div class="p-absolute page bg-gradient-orange">

        <img v-for="item in emblemType" class="preload" :src="'img/'+item.type+'.png'" :key="item.type">

		<div class="content" v-cloak>
            <template v-for="(item, index) in emblems">
                <div v-if="item.player!='No candidate'" class="emblem-container p-relative" :key="index">
                    <img :src="'img/'+item.type+'.png'" class="logo">
                    <div class="right">
                        <div class="header truncate">{{item.player}}</div>
                        <div class="text-small">{{item.desc}}</div>
                    </div>
                </div>
            </template>

            <div class="table-header d-flex center" v-if="unclaimed">Unclaimed Emblem<template v-if="unclaimed>1">s</template></div>

            <template v-for="(item, index) in emblems">
                <div class="table-row" v-if="item.player=='No candidate'" :key="index">{{item.desc}}</div>
            </template>

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
		padding-left: 70px;
		background-color: #616161;
		color: white;
	}
	.text-small {
		padding: 10px;
		padding-left: 70px;
		background-color: white;
		font-style: italic;
	}
    .emblem-container + .table-header {
        margin-top: 15px;
    }
    .table-header + .table-row {
        margin-top: 10px;
    }
</style>

<script>
export default {
	data() {
		return {
			emblems: [],
			emblemType: [
				{type:'emblem_dragon', desc:'Acquired 5 winning streak'},
				{type:'emblem_shark', desc:'Acquired 3 winning streak'},
				{type:'emblem_chicken', desc:'Acquired 3 losing streak'},
				{type:'emblem_brutality', desc:'Never lose any set in 3 matches'},
				{type:'emblem_dragonslayer', desc:'Non dragon beats dragon player'},
				{type:'emblem_sharkslayer', desc:'Non dragon/shark beats shark player'},
				{type:'emblem_weekend', desc:'We love to play on weekend'},
				{type:'emblem_sleeping', desc:'Not playing for more than 5 days'}
            ],
            unclaimed: 0,
		}
	},
	props: ['session', 'apiurl'],
	methods: {
		getEmblems() {
			var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)

			fetch(self.apiurl+'emblemGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {

					self.emblems.push({
						type: 'emblem_king',
						desc: 'Longest winning streak '+response.emblem_king.WIN_STREAK,
						player: response.emblem_king.NAME,
                    })
                    if(response.emblem_king.NAME=='No candidate') self.unclaimed++
                    

					self.emblemType.forEach(function(emblem){
						for(var i=0; i<response[emblem.type].length; i++) {
							self.emblems.push({
								type: emblem.type,
								desc: emblem.desc,
								player: response[emblem.type][i],
							})
                        }
                        if(response[emblem.type][0]=='No candidate') self.unclaimed++
					})
				}
				else {
					console.log('Fail to get emblems')
				}
			})
		},
	},
	mounted() {
		this.getEmblems()
	}
};
</script>