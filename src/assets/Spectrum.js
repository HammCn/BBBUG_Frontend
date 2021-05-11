let Spectrum = {
    type: 0,
    audio: null,
    audioCtx: null,
    mediaElementSource: null,
    analyser: null,
    status: 1, //flag for sound is playing 1 or stopped 0
    animationId: null,
    allCapsReachBottom: false,
    $parentCont: null,
    $cont: null,
    $canvas: null,
    _init: function (audio) {
        try {
            let userAgent = navigator.userAgent;
            if (!/windows/i.test(userAgent) || (userAgent.indexOf('compatible') > -1 && userAgent.indexOf('MSIE') > -1)) {
                return;
            }
            //下面这些是为了统一Chrome和Firefox的AudioContext
            window.AudioContext = window.AudioContext || window.webkitAudioContext || window.mozAudioContext || window.msAudioContext;
            window.requestAnimationFrame = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.msRequestAnimationFrame;
            window.cancelAnimationFrame = window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.mozCancelAnimationFrame || window.msCancelAnimationFrame;

            let _this = this;
            _this.audio = audio;
            _this.$parentCont = document.getElementById('spectrumCont');
            if (!_this.$parentCont) {
                return;
            }
            _this.audio.crossOrigin = 'anonymous';

            _this.audioCtx = new window.AudioContext();

            _this.audio.addEventListener('play', (e) => {
                _this.on();
            });
            _this.audio.addEventListener('pause', (e) => {
                _this.off();
            });

            _this.audio.addEventListener('timeupdate', (e) => {
                if (!_this.mediaElementSource) {
                    _this.on();
                }
            });

            function onResizeFunction() {
                _this.on();
            }

            document.body.removeEventListener("resize", onResizeFunction);
            document.body.addEventListener("resize", onResizeFunction);
            window.removeEventListener("resize", onResizeFunction);
            window.addEventListener("resize", onResizeFunction);

        } catch (e) {
            console.error(e);
        }
    },
    on: function (audio) {
        let _this = this;
        if (_this.audioCtx && _this.audioCtx.state === 'running') {
            if (!_this.mediaElementSource) {
                _this.mediaElementSource = _this.audioCtx.createMediaElementSource(_this.audio);
                _this.analyser = _this.audioCtx.createAnalyser();
                _this.mediaElementSource.connect(_this.analyser);
                _this.analyser.connect(_this.audioCtx.destination);
            }
            _this.status = 1;
            _this._render(_this.type);
        } else if (audio) {
            _this._init(audio);
        } else if (_this.audio) {
            _this._init(_this.audio);
        } else {
            _this.off();
        }
    },
    off: function () {
        let _this = this;
        if (_this.audioCtx && _this.audioCtx.state === 'running') {
            let _this = this;
            // _this.analyser && _this.analyser.disconnect(_this.audioCtx.destination);
            _this.status = 0;
        }
    },
    changeType(type) {
        if (isNaN(type)) {
            return;
        }
        type = type * 1;
        let _this = this;
        if (_this.audioCtx && _this.audioCtx.state === 'running') {
            cancelAnimationFrame(_this.animationId); //since the sound is stoped and animation finished, stop the requestAnimation to prevent potential memory leak,THIS IS VERY IMPORTANT!
            _this.$cont && _this.$cont.remove();
            _this._render(type);
        }
    },
    _render: function (type) {
        let _this = this;
        var refContWidth = _this.$parentCont.clientWidth;
        var refContHeight = _this.$parentCont.clientHeight;

        let setting = {
            width: refContWidth,
            height: refContHeight * 1,
            top: refContHeight * 1
        };

        _this.$cont && _this.$cont.remove();

        _this.$cont = document.createElement('div');
        _this.$cont.style.width = setting.width;
        _this.$cont.style.height = setting.height;
        _this.$cont.style.position = 'absolute';
        _this.$cont.style.top = setting.top;
        _this.$cont.style.zIndex = 99;

        _this.$canvas = document.createElement('canvas');
        _this.$canvas.width = setting.width;
        _this.$canvas.style.width = setting.width;
        _this.$canvas.height = setting.height;
        _this.$canvas.style.height = setting.height;

        _this.$cont.append(_this.$canvas);
        _this.$parentCont.append(_this.$cont);


        function contClickFunction() {
            _this.changeType(_this.type + 1);
        }

        _this.$cont.removeEventListener('click', contClickFunction)
        _this.$cont.addEventListener('click', contClickFunction)

        let fftSize = 0;
        let cavWidth = _this.$canvas.width;
        for (let i = 3; i < 16; i++) {
            fftSize = Math.pow(2, i);
            if (cavWidth < fftSize) {
                break;
            }
        }
        if (!_this.analyser) {
            return;
        }
        _this.analyser.fftSize = fftSize;

        // let bufferLength = analyser.frequencyBinCount;
        // let dataArray = new Uint8Array(bufferLength);

        _this._draw(type);
    },
    _draw: function (type) {
        let _this = this;
        //颜色数组
        let colorArray = ['#f82466', '#00FFFF', '#AFFF7C', '#FFAA6A', '#6AD5FF', '#D26AFF', '#FF6AE6', '#FF6AB8', '#FF6A6A'];
        //颜色随机数
        let colorRandom = Math.floor(Math.random() * colorArray.length);
        //效果随机数
        let effectRandom = Math.floor(Math.random() * 1);
        //随机选取颜色
        let color = colorArray[colorRandom];
        //随机选取效果
        switch (type) {
            case 0:
                //条形
                _this.type = type;
                _this._draw_bar(color);
                break;
            case 1:
                _this.type = type;
                _this._draw_bar2(color);
                break;
            case 2:
                _this.type = type;
                _this._draw_line(color);
                break;
            default:
                //条形
                _this.type = 0;
                _this._draw_bar(color);
                break;
        }
    },
    _draw_line: function (color) {
        let _this = this;
        let analyser = _this.analyser;
        let canvas = _this.$canvas;
        let width = canvas.width;
        let height = canvas.height;
        let canvasCtx = canvas.getContext('2d');

        const bufferLength = this.analyser.fftSize; // 默认为2048
        const dataArray = new Uint8Array(bufferLength);

        let _draw_meter = function () {
            analyser.getByteTimeDomainData(dataArray);// 将音频信息存储在长度为2048（默认）的类型数组（dataArray）

            if (_this.status === 0) {
                //fix when some sounds end the value still not back to zero
                for (let i = dataArray.length - 1; i >= 0; i--) {
                    dataArray[i] = 0;
                }
                _this.allCapsReachBottom = true;
                for (let i = dataArray.length - 1; i >= 0; i--) {
                    _this.allCapsReachBottom = _this.allCapsReachBottom && (dataArray[i] === 0);
                }
                if (_this.allCapsReachBottom) {
                    cancelAnimationFrame(_this.animationId); //since the sound is stoped and animation finished, stop the requestAnimation to prevent potential memory leak,THIS IS VERY IMPORTANT!
                    _this.$cont && _this.$cont.remove();
                    return;
                }
            }

            canvasCtx.clearRect(0, 0, width, height);
            // canvasCtx.fillStyle = 'rgba(255, 255, 255, 0)';//background color
            // canvasCtx.fillRect(0, 0, width, height);
            canvasCtx.lineWidth = 2;
            //['#f82466', '#00FFFF', '#AFFF7C', '#ffaa6a', '#6AD5FF', '#D26AFF', '#FF6AE6', '#FF6AB8', '#FF6A6A']
            canvasCtx.strokeStyle = '#6AD5FF';//colorArray[Math.floor(Math.random() * 10)];//'#dc143c';
            canvasCtx.beginPath();

            const sliceWidth = Number(width) / bufferLength;
            let x = 0;
            canvasCtx.moveTo(x, height / 2);
            for (let i = 0; i < bufferLength; i++) {
                const v = dataArray[i] / 128.0;
                const y = v * height / 2;
                canvasCtx['lineTo'](x, y);
                x += sliceWidth;
            }
            canvasCtx.lineTo(width, height / 2);
            canvasCtx.stroke();

            _this.animationId = window.requestAnimationFrame(_draw_meter); // 定时动画
        };

        _this.animationId = window.requestAnimationFrame(_draw_meter); // 定时动画
    },
    _draw_bar: function (color) {
        let _this = this;

        // let colorArray = ['#f82466', '#00FFFF', '#AFFF7C', '#ffaa6a', '#6AD5FF', '#D26AFF', '#FF6AE6', '#FF6AB8', '#FF6A6A'];

        let analyser = _this.analyser;
        let canvas = _this.$canvas;
        let cwidth = canvas.width,
            cheight = canvas.height;

        // console.log(cheight);

        analyser.fftSize = 1024;
        var bufferLength = analyser.frequencyBinCount;
        let dataArray = new Uint8Array(bufferLength);
        let slowDownDataArray = [];

        let canvasCtx = canvas.getContext('2d');
        let linearGradient = canvasCtx.createLinearGradient(0, 0, 0, cheight);
        linearGradient.addColorStop(1, 'rgb(175,255,124)');
        linearGradient.addColorStop(0.3, 'rgb(255,170,106,1)');
        linearGradient.addColorStop(0, 'rgb(248,36,102,1)');

        canvasCtx.clearRect(0, 0, cwidth, cheight);

        let _draw_meter = function () {

            analyser.getByteFrequencyData(dataArray);

            if (_this.status === 0) {
                //fix when some sounds end the value still not back to zero
                for (let i = dataArray.length - 1; i >= 0; i--) {
                    dataArray[i] = 0;
                }
                _this.allCapsReachBottom = true;
                for (let i = slowDownDataArray.length - 1; i >= 0; i--) {
                    _this.allCapsReachBottom = _this.allCapsReachBottom && (slowDownDataArray[i] === 0);
                }
                if (_this.allCapsReachBottom) {
                    cancelAnimationFrame(_this.animationId); //since the sound is stoped and animation finished, stop the requestAnimation to prevent potential memory leak,THIS IS VERY IMPORTANT!
                    _this.$cont && _this.$cont.remove();
                    return;
                }
            }

            // let backgroundColor = '#292a2d';//document.body.style.backgroundColor || 'rgb(255,255,255)';
            // canvasCtx.fillStyle = 'rgb()';
            // canvasCtx.fillRect(0, 0, cwidth, cheight);
            canvasCtx.clearRect(0, 0, cwidth, cheight);

            let barWidth = (cwidth / bufferLength) * 2.5;
            let barHeight;
            let x = 0;

            for (let i = 0; i < bufferLength; i++) {

                barHeight = cheight * Number.parseFloat(dataArray[i] / 255.00).toFixed(2);

                if (slowDownDataArray.length < Math.round(bufferLength)) {
                    slowDownDataArray.push(barHeight);
                }

                //['#f82466', '#00FFFF', '#AFFF7C', '#ffaa6a', '#6AD5FF', '#D26AFF', '#FF6AE6', '#FF6AB8', '#FF6A6A']
                canvasCtx.fillStyle = '#6AD5FF';//colorArray[Math.floor(Math.random() * 10)];//'#dc143c';
                //draw the cap, with transition effect
                if (barHeight < slowDownDataArray[i]) {
                    canvasCtx.fillRect(x, cheight - (--slowDownDataArray[i]), barWidth, 2);
                } else {
                    canvasCtx.fillRect(x, cheight - barHeight, barWidth, 2);
                    slowDownDataArray[i] = barHeight;
                }

                canvasCtx.fillStyle = linearGradient;
                canvasCtx.fillRect(x, cheight - barHeight + 4, barWidth, barHeight);

                x += barWidth + 1;
            }

            _this.animationId = requestAnimationFrame(_draw_meter);

        };
        _this.animationId = requestAnimationFrame(_draw_meter);

    },
    _draw_bar2: function (color) {
        let _this = this;

        // let colorArray = ['#f82466', '#00FFFF', '#AFFF7C', '#ffaa6a', '#6AD5FF', '#D26AFF', '#FF6AE6', '#FF6AB8', '#FF6A6A'];

        let analyser = _this.analyser;
        let canvas = _this.$canvas;
        let cwidth = canvas.width,
            cheight = canvas.height;

        // console.log(cheight);

        // analyser.fftSize = 256;
        var bufferLength = analyser.frequencyBinCount;
        let dataArray = new Uint8Array(bufferLength);
        let slowDownDataArray = [];

        let canvasCtx = canvas.getContext('2d');
        let linearGradient = canvasCtx.createLinearGradient(0, 0, 0, cheight);
        linearGradient.addColorStop(1, 'rgb(175,255,124)');
        linearGradient.addColorStop(0.3, 'rgb(255,170,106,1)');
        linearGradient.addColorStop(0, 'rgb(248,36,102,1)');

        canvasCtx.clearRect(0, 0, cwidth, cheight);

        let _draw_meter = function () {

            analyser.getByteFrequencyData(dataArray);

            if (_this.status === 0) {
                //fix when some sounds end the value still not back to zero
                for (let i = dataArray.length - 1; i >= 0; i--) {
                    dataArray[i] = 0;
                }
                _this.allCapsReachBottom = true;
                for (let i = slowDownDataArray.length - 1; i >= 0; i--) {
                    _this.allCapsReachBottom = _this.allCapsReachBottom && (slowDownDataArray[i] === 0);
                }
                if (_this.allCapsReachBottom) {
                    cancelAnimationFrame(_this.animationId); //since the sound is stoped and animation finished, stop the requestAnimation to prevent potential memory leak,THIS IS VERY IMPORTANT!
                    _this.$cont && _this.$cont.remove();
                    return;
                }
            }

            // let backgroundColor = '#292a2d';//document.body.style.backgroundColor || 'rgb(255,255,255)';
            // canvasCtx.fillStyle = backgroundColor;
            // canvasCtx.fillRect(0, 0, cwidth, cheight);
            canvasCtx.clearRect(0, 0, cwidth, cheight);

            let barWidth = (cwidth / bufferLength) * 2.5;
            let barHeight;
            let x = 0;

            for (let i = 0; i < bufferLength; i++) {
                barHeight = cheight * Number.parseFloat(dataArray[i] / 255.00).toFixed(2);

                if (slowDownDataArray.length < Math.round(bufferLength)) {
                    slowDownDataArray.push(barHeight);
                }

                //['#f82466', '#00FFFF', '#AFFF7C', '#ffaa6a', '#6AD5FF', '#D26AFF', '#FF6AE6', '#FF6AB8', '#FF6A6A']
                canvasCtx.fillStyle = '#6AD5FF';//colorArray[Math.floor(Math.random() * 10)];//'#dc143c';
                //draw the cap, with transition effect
                if (barHeight < slowDownDataArray[i]) {
                    canvasCtx.fillRect(x, cheight - (--slowDownDataArray[i]), barWidth, 2);
                } else {
                    canvasCtx.fillRect(x, cheight - barHeight, barWidth, 2);
                    slowDownDataArray[i] = barHeight;
                }

                canvasCtx.fillStyle = linearGradient;
                canvasCtx.fillRect(x, cheight - barHeight + 4, barWidth, barHeight);

                x += barWidth - 1;
            }

            _this.animationId = requestAnimationFrame(_draw_meter);

        };
        _this.animationId = requestAnimationFrame(_draw_meter);

    },
};
export default Spectrum;
