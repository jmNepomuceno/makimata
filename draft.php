<?php
    include("./assets/connection/connection.php");
    session_start();

    $sql = "SELECT 
                *
            FROM provinces LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // $stmtUpdate = $pdo->prepare("UPDATE tutorials SET status = 'pending'");
    // $stmtUpdate->execute();

    // $orderId = "ORD000004";

    // // fetch order + customer + items
    // $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
    // $stmt->execute([$orderId]);
    // $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_code = ?");
    // $stmt->execute([$orderId]);
    // $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // echo "<pre>"; print_r($order); echo "</pre>";
    echo "<pre>"; print_r($data); echo "</pre>";
    // echo "<pre>"; print_r($_SESSION); echo "</pre>";
    
?>



<!-- 
2. sa customization dapat hindi fix yung choices dadagdagan pa ng other customization option

3. sa cost estimation dapat naka break down yung price ng materials na pinili ng customer upon customization

12. linawin yung mga nakalagay sa status ng order
 -->

<!-- 
1. sa login dapat wala yung admin? na part
(dapat isa lang yung login for user and admin?) = DONE

5. gawing input type yung quantity = DONE 

9. upon loggin in or pag bukas ng user ng web dapat products agad ang bubungad = DONE

10. billing invoice yung nakalagay hindi receipt = DONE

11. sa ratings dapat nakikita yung average ng ratings = DONE

8. sa delivery address paano kung sa ibang bahay gusto ipadala ng customer = DONE

6. dapat dynamic at hindi hard coded yung ibang part like sa featured products (dapat manageable lahat) = DONE
-->

<!-- 
4. sa tutorial page gagawin nalang na allowing customer to upload ng tutorial at i aapprove nalang ng admin

7. paano mag rereachout yung customer if gusto nila ng 1k orders pero ang stock is 40 lang
-->

<!-- ✅ 1. sa login dapat wala yung admin? na part (dapat isa lang yung login for user and admin?)
✅ 5. gawing input type yung quantity
✅ 9. upon loggin in or pag bukas ng user ng web dapat products agad ang bubungad
✅ 10. billing invoice yung nakalagay hindi receipt
✅ 11. sa ratings dapat nakikita yung average ng ratings
✅ 8. sa delivery address paano kung sa ibang bahay gusto ipadala ng customer
✅ 6. dapat dynamic at hindi hard coded yung ibang part like sa featured products (dapat manageable lahat)
✅ 12. linawin yung mga nakalagay sa status ng order

4. sa tutorial page gagawin nalang na allowing customer to upload ng tutorial at i aapprove nalang ng admin
7. paano mag rereachout yung customer if gusto nila ng 1k orders pero ang stock is 40 lang


2. sa customization dapat hindi fix yung choices dadagdagan pa ng other customization option
3. sa cost estimation dapat naka break down yung price ng materials na pinili ng customer upon customization -->


<!-- ✅1. yung privacy policy sa create account walang laman
✅2. hindin consistent yung header design
✅6. yung wishlist hard coded dapat sha yung nag rereflect sa home page sa customers favorite hindi naaalis sa wishlist kapag inadd to cart na
3. make the product more informative lalo na sa size kasi standard lang yung may preview ng size

✅4. delivery date and sa scope ng delivery bakit 50 pesos lang na fix kung buong pinas
✅5. pag nag bubulk orders hindi accurate total yung price
✅7. sa status dapat hindi na bumabalik sa previous status -->


<!--  -->
<!-- 
1. sa order 1 item lang yung nag rereflect sa admin side kahit more than yung plinace order ko

3. working na po yung sms kaya if ma iimplement mas okqy

4. alisin nalang yung gift packaging and special handling sa delivery preference naman alisin nalang ying scheduled date
5. aalisin din po yung e wallet sa payment

6. sa tutorial hindi pa rin nag peplay yung vid kahit naka edit na yung sa line 157 sa contact.js

✅7. sa cost etimation po is dapat papa naka breakdown po yung sa pricing from the size finish engraving dapat nakalagay sa footer kasama yung estimated price
8. sa billing invoice hindi accurate yung total

9. sa wishlist hindi naalis yung nasa wishlist kahit inadd to cart na
10. tas lastly po sa password creation dapat may password requirements 
-->