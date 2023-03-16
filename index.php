#!ipxe
set menu-timeout 5000
set submenu-timeout ${menu-timeout}

:start
menu yourtexthere iPXE Boot Menu
item --gap --  ------------------------------------------------------------
item --key a bcld1 (a) Facet uBCLD F11 (via Facet serverveerder lokaal)
#item --key a bcld3 (k) Facet uBCLD F11 (via Facet serverveerder lokaal) Geen URL
item --key a bcld4 (l) Facet uBCLD F11 (via Facet serverveerder lokaal + FAO)
item --key b bcld2 (b) Facet uBCLD F11 (via iPXE server centraal)
item --gap --  ------------------------------------------------------------
item --key w WDS (w) SCCM (DP Ondersteuningbureau)
item --key x WDS2 (x) SCCM (SCCM centraal)
item --key w WDSFAST (p) SCCM HTTPboot
item --key z Clonezilla (z) Clonezilla live
item --gap --  ------------------------------------------------------------
item --key o exit1 (o) Start Windows middels exit commando
item --gap --  ------------------------------------------------------------
choose --default exit1 --timeout 5000 selected || reboot
goto ${selected}

# Getting to this part is not a good thing
reboot


:bcld1
kernel http://iphere:8666/facet/VMLINUZ initrd=INITRD rhgb boot=casper ip=dhcp toram set bcldparameters="quiet loglevel=0 systemd.show_status=auto rd.udev.log_priority=3 selinux=0" bcld.display.preset=1080p url=http://iphere:8666/facet/bcld.iso
initrd http://iphere:8666/facet/INITRD
boot
goto start

:bcld2
kernel http://iphere/facet/VMLINUZ initrd=INITRD rhgb boot=casper ip=dhcp toram set bcldparameters="quiet loglevel=0 systemd.show_status=auto rd.udev.log_priority=3 selinux=0 bcld.afname.url=https://afname.facet.onl/facet-player-assessment" url=http://1iphere/facet/bcld.iso
initrd http://iphere/facet/INITRD
boot
goto start

:bcld3
kernel http://iphere:8666/facet/VMLINUZ initrd=INITRD rhgb boot=casper ip=dhcp toram set bcldparameters="quiet loglevel=0 systemd.show_status=auto rd.udev.log_priority=3 selinux=0 bcld.display.preset=1080p"  url=http://iphere:8666/facet/bcld.iso
initrd http://iphere:8666/facet/INITRD
boot
goto start

:bcld4
kernel http://10-100.ydns.eu:8666/facet/VMLINUZ initrd=INITRD rhgb boot=casper ip=dhcp toram set bcldparameters="quiet loglevel=0 systemd.show_status=auto rd.udev.log_priority=3 selinux=0 bcld.display.preset=1080p bcld.afname.url=https://iphere/facet-afname" url=http://iphere:8666/facet/bcld.iso
initrd http://10-100.ydns.eu:8666/facet/INITRD
boot
goto start

:exit1
exit

:shell
echo Type 'exit' to get back to the menushell
shell
goto start

:WDS
# doorlussen naar mijn MDT/WDS bootmenu
set netX/next-server iphere
iseq ${platform} efi && imgexec tftp://${netX/next-server}/smsboot\O2G00003\x64\wdsmgfw.efi || imgexec tftp://${netX/next-server}/smsboot\O2G00003\x64\wdsnbp.com
exit 1

:WDS2
# doorlussen naar mijn MDT/WDS bootmenu
set netX/next-server iphere
iseq ${platform} efi && imgexec tftp://${netX/next-server}/smsboot\O2G00003\x64\wdsmgfw.efi || imgexec tftp://${netX/next-server}/smsboot\O2G00003\x64\wdsnbp.com
exit 1


:WDSFAST
set boot-url http://iphere
kernel ${boot-url}/wimboot
initrd ${boot-url}/bootmgr bootmgr
initrd ${boot-url}/boot/BCD bcd
initrd ${boot-url}/boot/boot.sdi boot.sdi
# initrd ${boot-url}/sources/HTTPboot.wim HTTPboot.wim
initrd http://iphere/sources/HTTPboot.wim HTTPboot.wim
boot
boot || prompt --key s --timeout 10000 Chainloading failed, hit 's' for the iPXE shell; reboot in 10 seconds && shell || reboot

:Clonezilla
set boot-url http://10.200.40.80
kernel ${boot-url}/tools/clonezilla-live/vmlinuz initrd=initrd.img boot=live config noswap nolocales edd=on nomodeset vga=788 nosplash noprompt fetch=http://10.200.40.80/tools/clonezilla-live/filesystem.squashfs
initrd ${boot-url}/tools/clonezilla-live/initrd.img
boot
