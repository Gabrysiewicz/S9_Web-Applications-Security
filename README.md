<table align='center'>
  <tr> <td colspan='3' align='center' width='884px'> Web Applications Security </td> </tr>
  <tr> <td colspan="3" align='center'> <img src='https://github.com/Gabrysiewicz/Programowanie-aplikacji-w-chmurze-obliczeniowe/blob/main/logo_politechniki_lubelskiej.jpg' width="400px" height="400px"></td> </tr>
  <tr> <td> Kamil Gabrysiewicz </td> <td> Index: 95400 </td> <td> Grupa: 2.1 </td> </tr>  
  <tr> <td> Wtorek 11:45-13:15 </td> <td> Semestr 2 </td> <td>Laboratorium 8</td></tr>  
</table>

# Task 10.1.
### Create two recipients and a sender. Post messages to the feed they subscribe to. 

### Disable one recipient. Post some messages. Re-enable the second recipient. Repeat the exercise for different values of QOS (0,1,2). Determine the impact of QOS on message delivery.

To create a recipient I have used the commad `mosquitto_sub` for 3 different recipients
```
mosquitto_sub -h localhost -t "test/topic" -q 0 -c -i "subscriber qos 0" 
mosquitto_sub -h localhost -t "test/topic" -q 1 -c -i "subscriber qos 1" 
mosquitto_sub -h localhost -t "test/topic" -q 2 -c -i "subscriber qos 2" 
```
```
-h localhost: Specifies the MQTT broker's host.
-t "test/topic": Specifies the topic to which the subscriber is subscribing.
-q 0: Specifies the Quality of Service (QoS) level for the subscription
   0: At most once (no confirmation of delivery).
   1: At least once (guarantees message delivery, but duplicates may occur).
   2: Exactly once (ensures message delivery without duplicates).
-c: Enables clean session to be disabled, persistence is enabled
-i "subscriber qos 0": Specifies the client ID for the subscriber.
```

To send a message I have used the command `mosquitto_pub` with 3 different QoS every time.
```
mosquitto_pub -h localhost -t "test/topic" -m "Message at QoS 0" -q 0
mosquitto_pub -h localhost -t "test/topic" -m "Message at QoS 1" -q 1
mosquitto_pub -h localhost -t "test/topic" -m "Message at QoS 2" -q 2
```

<p align='center'>
  <img src="#">
</p>
<p align='center'>
  <img src="#">
</p>
