<template>
	<div class="p-absolute page bg-gradient-red">
		
		<img v-for="item in standings" class="preload" :src="item.USER_IMAGE" :key="item.PLAYER_ID">

		<div class="navbar d-flex">
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center">
					<span class="text-small">Completed</span>
					<div class="red">{{completed}}</div>
				</div>
			</div>
			<div class="nav-col p-relative d-flex">
				<div class="btn-circle"><span>{{percentage}}%</span></div>
			</div>
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center">
					<span class="text-small">Incomplete</span>
					<div class="red">{{incomplete}}</div>
				</div>
			</div>
		</div>

		<div class="content">
			<div class="table-header p-fixed">
				<div class="table-col-1">#</div>
                <template v-if="session.ARENA_ID=='000000'">
                    <div class="table-col-max">PLAYER</div>
                    <div class="table-col-1-5 text-center">E</div>
                    <div class="table-col-1-5 text-center">G</div>
                    <div class="table-col-1-5 text-center">%</div>
                </template>
                <template v-else>
				    <div class="table-col-max">PLAYER</div>
                    <div class="table-col-1 text-center"></div><!-- icon level -->
                    <div class="table-col-1 text-center">G</div>
                    <div class="table-col-1 text-center">W</div>
                    <div class="table-col-1 text-center">L</div>
                </template>
			</div>
			<div class="table-row" v-for="(item, index) in standings" :key="item.USER_ID" @click="showModalProfile(item.USER_ID)">
				<div class="table-col-1">{{index+1}}</div>
                <template v-if="session.ARENA_ID=='000000'">
				    <div class="table-col-max truncate">{{item.USER_NAME}}</div>
                    <div class="table-col-1-5 text-center">{{item.ELO}}</div>
                    <div class="table-col-1-5 text-center">{{item.GAME}}</div>
                    <div class="table-col-1-5 text-center">{{item.PERCENTAGE}}</div>
                </template>
                <template v-else>
                    <div class="table-col-max truncate">{{item.USER_NAME}}</div>
                    <div class="table-col-1 p-relative"><img v-if="item.LEVEL" class="icon-level" :src="'img/icons8-'+item.LEVEL+'-50.png'"></div>
                    <div class="table-col-1 text-center">{{item.WIN+item.LOSE}}</div>
                    <div class="table-col-1 text-center">{{item.WIN}}</div>
                    <div class="table-col-1 text-center">{{item.LOSE}}</div>
                </template>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalProfile.show}" @click.self="modalProfile.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalProfile.show=false">close</i>
				<template v-if="modalProfile.LEVEL">
					<img class="bg-level" :src="'img/emblem_'+modalProfile.LEVEL+'.png'">
				</template>
				<div class="modal-header red truncate">
					{{modalProfile.USER_NAME}}<br><br>
					<div class="p-relative d-inline-block">
						<img class="profilepic" :src="modalProfile.USER_IMAGE">
						<input v-if="modalProfile.USER_ID==session.USER_ID || session.USER_ID==1" id="inputProfilepic" type="file" accept="image/jpeg" @change="uploadProfilepic">
					</div>
				</div>
                <table width="100%" border="0" cellspacing="0" class="mt-10">
                    <tr>
                        <td>WIN STREAK</td>
                        <td>
                            <div class="d-flex space-between" style="margin:0 -2px">
                                <i v-for="i in (modalProfile.WIN_STREAK)" class="material-icons-outlined red" :key="'s'+i">star</i>
                                <i v-for="i in (5-modalProfile.WIN_STREAK)" class="material-icons-outlined" :key="'o'+i">star_outline</i>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="(value, key) in modalRate.type" :key="key" @click="showModalRate">
                        <td>{{key}}</td>
                        <td v-if="modalProfile.show">
                            <div class="d-flex space-between" style="margin:0 -2px">
                                <i v-for="i in modalProfile.STAT_AVERAGE[key]" class="material-icons-outlined red" :key="'s'+i">star</i>
                                <i v-for="i in (5-modalProfile.STAT_AVERAGE[key])" class="material-icons-outlined" :key="'o'+i">star_outline</i>
                            </div>
                        </td>
                    </tr>
                </table>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalRate.show}" @click.self="modalRate.show=false">
			<div class="modal">
				<i class="material-icons-outlined btn-close" @click="modalRate.show=false">close</i>
				<div class="modal-header red truncate">{{modalProfile.USER_NAME}}</div>
				<div class="modal-row">Your review for this player</div>

                <table width="100%" border="0" cellspacing="0">
                    <tr v-for="(value, key) in modalRate.type" :key="key">
                        <td>{{key}}</td>
                        <td v-if="modalRate.show">
                            <div class="d-flex space-between" style="margin:0 -2px">
                                <i v-for="i in modalRate.type[key]" class="material-icons-outlined red" :key="'s'+i" @click="modalRate.type[key]=i">star</i>
                                <i v-for="i in (5-modalRate.type[key])" class="material-icons-outlined" :key="'o'+i" @click="modalRate.type[key]=i+modalRate.type[key]">star_outline</i>
                            </div>
                        </td>
                    </tr>
                </table>

				<div class="divider mt-10"></div>
				<div class="modal-footer d-flex center">
					<button class="bg-gradient-red" @click="saveModalRate"><i class="material-icons-outlined v-middle white">check</i> <span class="v-middle white">SAVE</span></button>
				</div>
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalTipsProfile.show}" @click.self="modalTipsProfile.show=false">
			<i class="material-icons-outlined btn-close white" @click="modalTipsProfile.show=false">close</i>
			<div class="p-fixed white tips-1 text-center"><img src="img/tips_click.svg"><br>Click here to change profilepic if this player is you.</div>
			<div class="p-fixed white tips-2 text-center">
				<img src="img/tips_click.svg"><br>
				Click here to review player including yourself. Player stats are based on average of all reviews.
			</div>
		</div>

		<div class="modal-overlay" :class="{'active':modalTipsStanding.show}" @click.self="modalTipsStanding.show=false">
			<i class="material-icons-outlined btn-close white" @click="modalTipsStanding.show=false">close</i>
			<div class="p-fixed white tips-3 text-center"><img src="img/tips_arrow.svg"><br>This is arena's completed matches percentage.</div>
			<div class="p-fixed white tips-4 text-center"><img src="img/tips_click.svg"><br>Click on player's name to see profile.</div>
			<!-- <div class="p-fixed white tips-4 text-center"><img src="img/tips_arrow.svg"><br>Player's standing is ordered by number of game win minus lose, followed by number of set win minus lose.<br><br>Players that yet to play will be placed at bottom.<br><br>Click on player's name to see profile.</div> -->
		</div>

	</div>
</template>

<style>
	.ct-series-a path.ct-slice-donut-solid {
		fill: #E9203E;
	}
	.ct-series-b path.ct-slice-donut-solid {
		fill: #9E9E9E;
	}
</style>
<style scoped>
	.tips-1 {
		top: 118px;
	}
	.tips-2 {
		top: 295px;
	}
	.tips-3 {
		top: 50px;
	}
	.tips-4 {
		top: 250px;
	}
    table td {
        padding: 5px 0;
        height: 28px;
    }
    table td:first-child {
        width: 50%;
    }
	.table-header + .table-row {
		margin-top: 62px;
	}
	.icon-level {
		position: absolute;
		height: 22px;
		top: -11px;
		left: 6px;
	}
	#inputProfilepic {
		position: absolute;
		opacity: 0;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
	.btn-circle span {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
	}
    .modal {
		margin-top: 24px;
	}
	.modal-header {
		z-index: 2;
	}
	.profilepic {
		width: 96px;
		height: 96px;
		border-radius: 50%;
		border: 6px solid #E0E0E0;
		margin-bottom: 4px;
	}
	.bg-level {
        position: absolute;
        top: 165px;
        left: 50%;
        height: 240px;
        -webkit-transform: translateX(-50%);
        transform: translateX(-50%);
        opacity: .07;
        z-index: 1;
        pointer-events: none;
	}
</style>

<script>
export default {
	data() {
		return {
			filter: {
				player1: '',
				player2: ''
			},
			standings: [],
			completed: 0,
			incomplete: 0,
			percentage: 0,
			modalProfile: {
				show: false,
				USER_ID: '',
				USER_NAME: '',
				USER_IMAGE: '',
				STAT_AVERAGE: {},
				STAT_REVIEW_BY_ME: {},
				LEVEL: '',
				WIN_STREAK: 0,
				LOSE_STREAK: 0,
			},
			modalRate: {
				show: false,
				type: {
					ATTACK: null,
					DEFENSE: null,
					SERVE: null,
					PLACEMENT: null,
					FOOTWORK: null,
				}
			},
			modalTipsProfile: {
				show: false,
			},
			modalTipsStanding: {
				show: false,
			},
		}
	},
	props: ['session', 'tips', 'apiurl'],
	methods: {
		getStandings() {
			var self = this
			var formData = new FormData()
			formData.append('PLAYER_ID', self.session.PLAYER_ID)
			formData.append('ARENA_ID', self.session.ARENA_ID)

			fetch(self.apiurl+'standingGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.completed = response.completed
					self.incomplete = response.incomplete
					self.percentage = response.percentage

					var taffy = TAFFY(response.standings)
					self.standings = taffy().order('PLAYED desc, GAME_DIFF desc, SET_DIFF desc').get()

					new Chartist.Pie('.btn-circle', {
						series: [response.completed, response.incomplete]
					}, {
						donut: true,
						donutWidth: 6,
						donutSolid: true,
						showLabel: false
					})
				}
				else {
					console.log('Fail to get standing')
				}
			})
		},
		showModalProfile(USER_ID) {
            if(this.session.ARENA_ID=='000000') {
                this.$emit('showToast', 'No profile data for data migration')
                return
            }

			for(var i=0; i<this.standings.length; i++) {
				if(this.standings[i].USER_ID==USER_ID) {
					this.modalProfile.USER_ID = this.standings[i].USER_ID
					this.modalProfile.PLAYER_ID = this.standings[i].PLAYER_ID
					this.modalProfile.USER_NAME = this.standings[i].USER_NAME
					this.modalProfile.USER_IMAGE = this.standings[i].USER_IMAGE
					this.modalProfile.STAT_AVERAGE = this.standings[i].STAT_AVERAGE
					this.modalProfile.STAT_REVIEW_BY_ME = this.standings[i].STAT_REVIEW_BY_ME
                    this.modalProfile.LEVEL = this.standings[i].LEVEL
                    this.modalProfile.WIN_STREAK = this.standings[i].WIN_STREAK
                    
                    if(this.modalProfile.WIN_STREAK>5) this.modalProfile.WIN_STREAK = 5
                    
					this.modalProfile.show = true
					break
				}
			}
			
			if(!this.tips.profile) {
				this.$emit('updateTips', 'profile')
				this.modalTipsProfile.show = true
			}
		},
		uploadProfilepic() {
			var self = this

			var filePicker = document.getElementById('inputProfilepic')
			if(!filePicker || !filePicker.files || filePicker.files.length <= 0) return
			var myFile = filePicker.files[0]

			var reader = new FileReader()
			reader.readAsDataURL(myFile)
			reader.onload = function() {

				var img = document.createElement('img');
				img.onload = function(){		
					//========================================================== resize
					var canvas = document.createElement('canvas')
					var ctx = canvas.getContext('2d')
					canvas.width = canvas.height = 96
					ctx.drawImage(this, 0, 0, canvas.width, canvas.width)
					var dataURI = canvas.toDataURL()

					//========================================================== upload
					var formData = new FormData()
					formData.append('USER_ID', self.modalProfile.USER_ID)
					formData.append('USER_IMAGE', dataURI)

					fetch(self.apiurl+'profileGetSet.php', {
						method: 'post',
						body: formData
					}).then(function(response) {
						return response.json()
					}).then(function(response) {
						if(response.status=='ok') {
							for(var i=0; i<self.standings.length; i++) {
								if(self.standings[i].USER_ID==self.session.USER_ID) {
									self.standings[i].USER_IMAGE = response.USER_IMAGE
									self.modalProfile.USER_IMAGE = self.standings[i].USER_IMAGE
									break
								}
							}
						}
						else {
							console.log('Fail to update profile')
						}
					})

				}
				img.src = reader.result
			}
			reader.onerror = function (error) {
				console.log('uploadProfilepic failed: ', error)
			}
		},
		showModalRate() {
			console.log(this.modalProfile.STAT_REVIEW_BY_ME)
			this.modalRate.type.ATTACK = this.modalProfile.STAT_REVIEW_BY_ME.ATTACK || 1
			this.modalRate.type.DEFENSE = this.modalProfile.STAT_REVIEW_BY_ME.DEFENSE || 1
			this.modalRate.type.SERVE = this.modalProfile.STAT_REVIEW_BY_ME.SERVE || 1
			this.modalRate.type.PLACEMENT = this.modalProfile.STAT_REVIEW_BY_ME.PLACEMENT || 1
			this.modalRate.type.FOOTWORK = this.modalProfile.STAT_REVIEW_BY_ME.FOOTWORK || 1
			this.modalRate.show = true
		},
		saveModalRate() {
			var self = this
			var formData = new FormData()
			formData.append('PLAYER_ID', self.session.PLAYER_ID)
			formData.append('STAT_TARGET_PLAYER_ID', self.modalProfile.PLAYER_ID)
			formData.append('STAT_ATTACK', self.modalRate.type.ATTACK)
			formData.append('STAT_DEFENSE', self.modalRate.type.DEFENSE)
			formData.append('STAT_SERVE', self.modalRate.type.SERVE)
			formData.append('STAT_PLACEMENT', self.modalRate.type.PLACEMENT)
			formData.append('STAT_FOOTWORK', self.modalRate.type.FOOTWORK)

			fetch(self.apiurl+'profileGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
					self.getStandings()
					self.modalProfile.show = false
					self.modalRate.show = false
				}
				else {
					console.log('Fail to set profile')
				}
			})
		}
	},
	mounted() {
		this.getStandings()
		
		if(!this.tips.standing) {
			this.$emit('updateTips', 'standing')
			this.modalTipsStanding.show = true
		}
	}
};
</script>