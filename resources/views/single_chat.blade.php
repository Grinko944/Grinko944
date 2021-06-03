@extends("layouts.app")

@section("title",  "Admin home - " . config("app.name"))

@section("content")

    <div class="single_chat">
        <h4>Переписка с {{ $user->name }} (email: {{ $user->email }})</h4>
        <div class="messages">
            {{--            <div class="message_wrapper my-message">--}}
            {{--                <div class="message">This is an message</div>--}}
            {{--                <div class="message_time">12:21</div>--}}
            {{--            </div>--}}
            {{--            <div class="message_wrapper receiver-message">--}}
            {{--                <div class="message">This is an message</div>--}}
            {{--                <div class="message_time">12:21</div>--}}
            {{--            </div>--}}
        </div>
        <div class="enter_message_wrapper">
            <div class="input_message">
                <input type="text" placeholder="Введите сообщение" id="inputMessage">
            </div>
            <img src="/img/ic_send.png" alt="" class="send_message_btn"/>
        </div>
    </div>

    <div class="toast">This is an test message in an toast</div>

    <script>

        class SingleChat {
            userId
            orderId
            fromId
            messages

            constructor(userId, orderId, fromId, messages) {
                this.userId = userId
                this.orderId = orderId
                this.fromId = fromId
                this.messages = messages
            }

            // ref = firebase.database().ref(`messages/${this.userId}/${this.orderId}`)

            sendMessage = message => {
                let ref = firebase.database().ref(`messages/${this.userId}/${this.orderId}`)
                if (message.length === 0) {
                    document.querySelector(".toast").style.display = "block"
                    document.querySelector(".toast").textContent = "Введите сообщение"
                } else {
                    document.querySelector(".toast").style.display = "none"
                    let timeE = new Date()
                    ref.push().set({
                        from_id: this.fromId,
                        message: message,
                        orderId: this.orderId,
                        time: timeE
                    })
                }
            }

            readMessages = () => {
                let ref = firebase.database().ref(`messages/${this.userId}/${this.orderId}`)
                let messagesArray = []
                ref.on("value", function (snapshot) {
                    let i = 0
                    messages.innerHTML = ""
                    snapshot.forEach(function (childSnapshot) {
                        let message = childSnapshot.child("message").val(),
                            time = childSnapshot.child("time").val(),
                            fromIdSnap = childSnapshot.child("from_id").val()

                        if (fromIdSnap === fromId) {
                            messages.innerHTML += `<div class="message_wrapper my-message">
                                                            <div class="message">${message}</div>
                                                        </div>`
                        } else if (fromIdSnap != fromId) {
                            messages.innerHTML += `<div class="message_wrapper receiver-message">
                                                            <div class="message">${message}</div>
                                                        </div>`
                        }
                    })
                })
            }
        }

        function $_GET(key) {
            var p = window.location.search;
            p = p.match(new RegExp(key + '=([^&=]+)'));
            return p ? p[1] : false;
        }

        let orderId = $_GET("orderId"),
            userId = $_GET("userId"),
            fromId = $_GET("fromId"),
            messages = document.querySelector(".messages"),
            sendBtn = document.querySelector(".send_message_btn"),
            inputMessage = document.querySelector("#inputMessage")

        let singleChat = new SingleChat(userId, orderId, fromId, messages)

        sendBtn.addEventListener("click", () => {
            singleChat.sendMessage(inputMessage.value)
            inputMessage.value = ""
        })

        singleChat.readMessages()
    </script>

@endsection
