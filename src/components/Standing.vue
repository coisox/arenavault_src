<template>
	<div class="p-absolute page bg-gradient-red">

        <div class="navbar d-flex">
			<div class="nav-col p-relative d-flex center">
				<div class="v-middle text-center">
					<span class="text-small">Completed</span>
					<div class="red">{{completed}}</div>
				</div>
			</div>
			<div class="nav-col p-relative d-flex">
				<div class="btn-circle bg-gradient-grey"><span>{{Math.round(completed/(completed+incomplete)*100)}}%</span></div>
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
				<div class="table-col-5">PLAYER</div>
				<div class="table-col-1 text-center"></div>
				<div class="table-col-1 text-center">G</div>
				<div class="table-col-1 text-center">W</div>
				<div class="table-col-1 text-center">L</div>
			</div>
			<div class="table-row" v-for="(item, index) in standings" :key="item.USER_ID" @click="showModalProfile(item)">
				<div class="table-col-1">{{index+1}}</div>
				<div class="table-col-5 truncate">{{item.USER_NAME}}</div>
				<div class="table-col-1 p-relative">
                    <template v-if="item.LEVEL"><img class="icon-level" :src="'img/level_'+item.LEVEL+'.png'"></template>
                </div>
				<div class="table-col-1 text-center">{{item.WIN+item.LOSE}}</div>
				<div class="table-col-1 text-center">{{item.WIN}}</div>
				<div class="table-col-1 text-center">{{item.LOSE}}</div>
			</div>
		</div>
        
        <div class="modal-overlay" :class="{'active':modal.show}" @click.self="modal.show=false">
			<div class="modal">
                <i class="material-icons-outlined btn-close" @click="modal.show=false">close</i>
                <template v-if="modal.LEVEL">
                    <img class="bg-level" :src="'img/level_'+modal.LEVEL+'.png'">
                </template>
				<div class="modal-header red truncate">
                    {{modal.USER_NAME}}<br><br>
                    <img class="profilepic" :src="modal.USER_IMAGE">
                </div>
                <div class="modal-row p-relative">
                    <div class="d-flex space-between p-absolute text-small"><span>Unlikely</span><span>Habit</span></div>
                    <b>CHOP</b><img class="xiom" :src="'img/xiom_'+modal.USER_PROFILE.CHOP+'.svg'">
                </div>
                <div class="modal-row p-relative">
                    <div class="d-flex space-between p-absolute text-small"><span>Unlikely</span><span>Habit</span></div>
                    <b>LOOP</b><img class="xiom" :src="'img/xiom_'+modal.USER_PROFILE.LOOP+'.svg'">
                </div>
                <div class="modal-row p-relative">
                    <div class="d-flex space-between p-absolute text-small"><span>Simple</span><span>Complex</span></div>
                    <b>SERVE</b><img class="xiom" :src="'img/xiom_'+modal.USER_PROFILE.SERVE+'.svg'">
                </div>
                <div class="modal-row p-relative">
                    <div class="d-flex space-between p-absolute text-small"><span>Control</span><span>Aggresive</span></div>
                    <b>SMASH</b><img class="xiom" :src="'img/xiom_'+modal.USER_PROFILE.SMASH+'.svg'">
                </div>
                <div class="modal-row p-relative">
                    <div class="d-flex space-between p-absolute text-small"><span>Random</span><span>Utilize</span></div>
                    <b>PLACING</b><img class="xiom" :src="'img/xiom_'+modal.USER_PROFILE.PLACING+'.svg'">
                </div>
                <div class="modal-row">
                    <b>WIN STREAK:</b> {{modal.WIN_STREAK>1?modal.WIN_STREAK:0}}
                    <b class="ml-30">LOSE STREAK:</b> {{modal.LOSE_STREAK>1?modal.LOSE_STREAK:0}}
                </div>
			</div>
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
    .icon-level {
        position: absolute;
        height: 24px;
        top: -12px;
        left: 0;
    }
    .btn-circle span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .modal-header {
        z-index: 2;
    }
    .profilepic {
        width: 96px;
        border-radius: 50%;
        border: 6px solid #E0E0E0;
        margin-bottom: 4px;
    }
    .bg-level {
        position: absolute;
        top: 180px;
        left: 50%;
        width: calc(100vw - 88px);
        transform: translateX(-50%);
        opacity: .1;
        z-index: 1;
    }
    .xiom {
        width: 100%;
    }
    .d-flex.space-between.p-absolute {
        width: 100%;
        top: 15px;
    }
    .d-flex.space-between.p-absolute span {
        background-color: white;
        padding: 0 5px;
    }
    .modal {
        margin-top: 24px;
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
            players: [],
            completed: 0,
            incomplete: 0,
            modal: {
                show: false,
                USER_NAME: '',
                USER_IMAGE: '',
                USER_PROFILE: {},
                LEVEL: '',
                WIN_STREAK: 0,
                LOSE_STREAK: 0,
            }
		};
    },
    props: ['session', 'apiurl'],
	methods: {
		getStanding() {
            var self = this
			var formData = new FormData()
			formData.append('ARENA_ID', self.session.ARENA_ID)

			fetch(self.apiurl+'standingGetSet.php', {
				method: 'post',
				body: formData
			}).then(function(response) {
				return response.json()
			}).then(function(response) {
				if(response.status=='ok') {
                    self.players = response.players
                    self.completed = response.completed
                    self.incomplete = response.incomplete

                    var taffy = TAFFY(response.standings)
                    self.standings = taffy().order('PLAYED desc, GAME_DIFF desc, SET_DIFF desc, POINT_DIFF desc').get()

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
        showModalProfile(item) {
            console.log('showModalProfile: ', item)
            for(var i=0; i<this.players.length; i++) {
                if(this.players[i].USER_ID==item.USER_ID) {
                    this.modal.USER_NAME = this.players[i].USER_NAME
                    this.modal.USER_IMAGE = this.players[i].USER_IMAGE
                    this.modal.USER_PROFILE = JSON.parse(this.players[i].USER_PROFILE)
                    this.modal.LEVEL = item.LEVEL
                    this.modal.WIN_STREAK = item.WIN_STREAK
                    this.modal.LOSE_STREAK = item.LOSE_STREAK
                    this.modal.show = true
                    
                    break
                }
            }
        }
	},
	mounted() {
        this.getStanding()
	}
};
</script>