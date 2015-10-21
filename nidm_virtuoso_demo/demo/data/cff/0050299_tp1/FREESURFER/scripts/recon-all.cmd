
 mri_convert /gl/med/tvanerp/abide/t1/mgz/0050299/mprage.mgz /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/orig/001.mgz 

#--------------------------------------------
#@# MotionCor Tue Mar 12 10:45:25 PDT 2013

 cp /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/orig/001.mgz /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/rawavg.mgz 


 mri_convert /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/rawavg.mgz /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/orig.mgz --conform 


 mri_add_xform_to_header -c /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/transforms/talairach.xfm /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/orig.mgz /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/orig.mgz 

#--------------------------------------------
#@# Talairach Tue Mar 12 10:45:43 PDT 2013

 talairach_avi --i orig.mgz --xfm transforms/talairach.auto.xfm 


 cp transforms/talairach.auto.xfm transforms/talairach.xfm 

#--------------------------------------------
#@# Talairach Failure Detection Tue Mar 12 10:48:06 PDT 2013

 talairach_afd -T 0.005 -xfm transforms/talairach.xfm 


 awk -f /data/apps/freesurfer/5.1.0/bin/extract_talairach_avi_QA.awk /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/transforms/talairach_avi.log 

#--------------------------------------------
#@# Nu Intensity Correction Tue Mar 12 10:48:07 PDT 2013

 mri_nu_correct.mni --i orig.mgz --o nu.mgz --uchar transforms/talairach.xfm --n 2 

#--------------------------------------------
#@# Intensity Normalization Tue Mar 12 10:57:00 PDT 2013

 mri_normalize -g 1 nu.mgz T1.mgz 

#--------------------------------------------
#@# Skull Stripping Tue Mar 12 11:00:30 PDT 2013

 mri_em_register -skull nu.mgz /data/apps/freesurfer/5.1.0/average/RB_all_withskull_2008-03-26.gca transforms/talairach_with_skull.lta 


 mri_watershed -T1 -brain_atlas /data/apps/freesurfer/5.1.0/average/RB_all_withskull_2008-03-26.gca transforms/talairach_with_skull.lta T1.mgz brainmask.auto.mgz 


 cp brainmask.auto.mgz brainmask.mgz 

#-------------------------------------
#@# EM Registration Tue Mar 12 11:33:35 PDT 2013

 mri_em_register -uns 3 -mask brainmask.mgz nu.mgz /data/apps/freesurfer/5.1.0/average/RB_all_2008-03-26.gca transforms/talairach.lta 

#--------------------------------------
#@# CA Normalize Tue Mar 12 12:18:41 PDT 2013

 mri_ca_normalize -c ctrl_pts.mgz -mask brainmask.mgz nu.mgz /data/apps/freesurfer/5.1.0/average/RB_all_2008-03-26.gca transforms/talairach.lta norm.mgz 

#--------------------------------------
#@# CA Reg Tue Mar 12 12:21:56 PDT 2013

 mri_ca_register -nobigventricles -T transforms/talairach.lta -align-after -mask brainmask.mgz norm.mgz /data/apps/freesurfer/5.1.0/average/RB_all_2008-03-26.gca transforms/talairach.m3z 

#--------------------------------------
#@# CA Reg Inv Wed Mar 13 01:18:46 PDT 2013

 mri_ca_register -invert-and-save transforms/talairach.m3z 

#--------------------------------------
#@# Remove Neck Wed Mar 13 01:20:42 PDT 2013

 mri_remove_neck -radius 25 nu.mgz transforms/talairach.m3z /data/apps/freesurfer/5.1.0/average/RB_all_2008-03-26.gca nu_noneck.mgz 

#--------------------------------------
#@# SkullLTA Wed Mar 13 01:22:51 PDT 2013

 mri_em_register -skull -t transforms/talairach.lta nu_noneck.mgz /data/apps/freesurfer/5.1.0/average/RB_all_withskull_2008-03-26.gca transforms/talairach_with_skull.lta 

#--------------------------------------
#@# SubCort Seg Wed Mar 13 01:54:13 PDT 2013

 mri_ca_label -align -nobigventricles norm.mgz transforms/talairach.m3z /data/apps/freesurfer/5.1.0/average/RB_all_2008-03-26.gca aseg.auto_noCCseg.mgz 


 mri_cc -aseg aseg.auto_noCCseg.mgz -o aseg.auto.mgz -lta /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri/transforms/cc_up.lta 0050299 

#--------------------------------------
#@# Merge ASeg Wed Mar 13 02:29:44 PDT 2013

 cp aseg.auto.mgz aseg.mgz 

#--------------------------------------------
#@# Intensity Normalization2 Wed Mar 13 02:29:44 PDT 2013

 mri_normalize -aseg aseg.mgz -mask brainmask.mgz norm.mgz brain.mgz 

#--------------------------------------------
#@# Mask BFS Wed Mar 13 02:36:46 PDT 2013

 mri_mask -T 5 brain.mgz brainmask.mgz brain.finalsurfs.mgz 

#--------------------------------------------
#@# WM Segmentation Wed Mar 13 02:36:51 PDT 2013

 mri_segment brain.mgz wm.seg.mgz 


 mri_edit_wm_with_aseg -keep-in wm.seg.mgz brain.mgz aseg.mgz wm.asegedit.mgz 


 mri_pretess wm.asegedit.mgz wm norm.mgz wm.mgz 

#--------------------------------------------
#@# Fill Wed Mar 13 02:43:02 PDT 2013

 mri_fill -a ../scripts/ponscc.cut.log -xform transforms/talairach.lta -segmentation aseg.auto_noCCseg.mgz wm.mgz filled.mgz 

#--------------------------------------------
#@# Tessellate lh Wed Mar 13 02:44:29 PDT 2013

 mri_pretess ../mri/filled.mgz 255 ../mri/norm.mgz ../mri/filled-pretess255.mgz 


 mri_tessellate ../mri/filled-pretess255.mgz 255 ../surf/lh.orig.nofix 


 rm -f ../mri/filled-pretess255.mgz 


 mris_extract_main_component ../surf/lh.orig.nofix ../surf/lh.orig.nofix 

#--------------------------------------------
#@# Smooth1 lh Wed Mar 13 02:44:47 PDT 2013

 mris_smooth -nw -seed 1234 ../surf/lh.orig.nofix ../surf/lh.smoothwm.nofix 

#--------------------------------------------
#@# Inflation1 lh Wed Mar 13 02:44:53 PDT 2013

 mris_inflate -no-save-sulc ../surf/lh.smoothwm.nofix ../surf/lh.inflated.nofix 

#--------------------------------------------
#@# QSphere lh Wed Mar 13 02:45:35 PDT 2013

 mris_sphere -q -seed 1234 ../surf/lh.inflated.nofix ../surf/lh.qsphere.nofix 

#--------------------------------------------
#@# Fix Topology lh Wed Mar 13 02:50:20 PDT 2013

 cp ../surf/lh.orig.nofix ../surf/lh.orig 


 cp ../surf/lh.inflated.nofix ../surf/lh.inflated 


 mris_fix_topology -mgz -sphere qsphere.nofix -ga -seed 1234 0050299 lh 


 mris_euler_number ../surf/lh.orig 


 mris_remove_intersection ../surf/lh.orig ../surf/lh.orig 


 rm ../surf/lh.inflated 

#--------------------------------------------
#@# Make White Surf lh Wed Mar 13 06:06:57 PDT 2013

 mris_make_surfaces -noaparc -whiteonly -mgz -T1 brain.finalsurfs 0050299 lh 

#--------------------------------------------
#@# Smooth2 lh Wed Mar 13 06:12:39 PDT 2013

 mris_smooth -n 3 -nw -seed 1234 ../surf/lh.white ../surf/lh.smoothwm 

#--------------------------------------------
#@# Inflation2 lh Wed Mar 13 06:12:44 PDT 2013

 mris_inflate ../surf/lh.smoothwm ../surf/lh.inflated 


 mris_curvature -thresh .999 -n -a 5 -w -distances 10 10 ../surf/lh.inflated 


#-----------------------------------------
#@# Curvature Stats lh Wed Mar 13 06:14:50 PDT 2013

 mris_curvature_stats -m --writeCurvatureFiles -G -o ../stats/lh.curv.stats -F smoothwm 0050299 lh curv sulc 

#--------------------------------------------
#@# Sphere lh Wed Mar 13 06:14:55 PDT 2013

 mris_sphere -seed 1234 ../surf/lh.inflated ../surf/lh.sphere 

#--------------------------------------------
#@# Surf Reg lh Wed Mar 13 07:45:52 PDT 2013

 mris_register -curv ../surf/lh.sphere /data/apps/freesurfer/5.1.0/average/lh.average.curvature.filled.buckner40.tif ../surf/lh.sphere.reg 

#--------------------------------------------
#@# Jacobian white lh Wed Mar 13 08:13:38 PDT 2013

 mris_jacobian ../surf/lh.white ../surf/lh.sphere.reg ../surf/lh.jacobian_white 

#--------------------------------------------
#@# AvgCurv lh Wed Mar 13 08:13:40 PDT 2013

 mrisp_paint -a 5 /data/apps/freesurfer/5.1.0/average/lh.average.curvature.filled.buckner40.tif#6 ../surf/lh.sphere.reg ../surf/lh.avg_curv 

#-----------------------------------------
#@# Cortical Parc lh Wed Mar 13 08:13:42 PDT 2013

 mris_ca_label -l ../label/lh.cortex.label -aseg ../mri/aseg.mgz -seed 1234 0050299 lh ../surf/lh.sphere.reg /data/apps/freesurfer/5.1.0/average/lh.curvature.buckner40.filled.desikan_killiany.2010-03-25.gcs ../label/lh.aparc.annot 

#--------------------------------------------
#@# Make Pial Surf lh Wed Mar 13 08:14:54 PDT 2013

 mris_make_surfaces -white NOWRITE -mgz -T1 brain.finalsurfs 0050299 lh 

#--------------------------------------------
#@# Surf Volume lh Wed Mar 13 08:28:24 PDT 2013

 mris_calc -o lh.area.mid lh.area add lh.area.pial 


 mris_calc -o lh.area.mid lh.area.mid div 2 


 mris_calc -o lh.volume lh.area.mid mul lh.thickness 

#-----------------------------------------
#@# Parcellation Stats lh Wed Mar 13 08:28:25 PDT 2013

 mris_anatomical_stats -mgz -cortex ../label/lh.cortex.label -f ../stats/lh.aparc.stats -b -a ../label/lh.aparc.annot -c ../label/aparc.annot.ctab 0050299 lh white 

#-----------------------------------------
#@# Cortical Parc 2 lh Wed Mar 13 08:28:39 PDT 2013

 mris_ca_label -l ../label/lh.cortex.label -aseg ../mri/aseg.mgz -seed 1234 0050299 lh ../surf/lh.sphere.reg /data/apps/freesurfer/5.1.0/average/lh.destrieux.simple.2009-07-29.gcs ../label/lh.aparc.a2009s.annot 

#-----------------------------------------
#@# Parcellation Stats 2 lh Wed Mar 13 08:30:01 PDT 2013

 mris_anatomical_stats -mgz -cortex ../label/lh.cortex.label -f ../stats/lh.aparc.a2009s.stats -b -a ../label/lh.aparc.a2009s.annot -c ../label/aparc.annot.a2009s.ctab 0050299 lh white 

#--------------------------------------------
#@# Tessellate rh Wed Mar 13 08:30:16 PDT 2013

 mri_pretess ../mri/filled.mgz 127 ../mri/norm.mgz ../mri/filled-pretess127.mgz 


 mri_tessellate ../mri/filled-pretess127.mgz 127 ../surf/rh.orig.nofix 


 rm -f ../mri/filled-pretess127.mgz 


 mris_extract_main_component ../surf/rh.orig.nofix ../surf/rh.orig.nofix 

#--------------------------------------------
#@# Smooth1 rh Wed Mar 13 08:30:34 PDT 2013

 mris_smooth -nw -seed 1234 ../surf/rh.orig.nofix ../surf/rh.smoothwm.nofix 

#--------------------------------------------
#@# Inflation1 rh Wed Mar 13 08:30:41 PDT 2013

 mris_inflate -no-save-sulc ../surf/rh.smoothwm.nofix ../surf/rh.inflated.nofix 

#--------------------------------------------
#@# QSphere rh Wed Mar 13 08:31:22 PDT 2013

 mris_sphere -q -seed 1234 ../surf/rh.inflated.nofix ../surf/rh.qsphere.nofix 

#--------------------------------------------
#@# Fix Topology rh Wed Mar 13 08:36:07 PDT 2013

 cp ../surf/rh.orig.nofix ../surf/rh.orig 


 cp ../surf/rh.inflated.nofix ../surf/rh.inflated 


 mris_fix_topology -mgz -sphere qsphere.nofix -ga -seed 1234 0050299 rh 


 mris_euler_number ../surf/rh.orig 


 mris_remove_intersection ../surf/rh.orig ../surf/rh.orig 


 rm ../surf/rh.inflated 

#--------------------------------------------
#@# Make White Surf rh Wed Mar 13 12:44:22 PDT 2013

 mris_make_surfaces -noaparc -whiteonly -mgz -T1 brain.finalsurfs 0050299 rh 

#--------------------------------------------
#@# Smooth2 rh Wed Mar 13 12:50:36 PDT 2013

 mris_smooth -n 3 -nw -seed 1234 ../surf/rh.white ../surf/rh.smoothwm 

#--------------------------------------------
#@# Inflation2 rh Wed Mar 13 12:50:41 PDT 2013

 mris_inflate ../surf/rh.smoothwm ../surf/rh.inflated 


 mris_curvature -thresh .999 -n -a 5 -w -distances 10 10 ../surf/rh.inflated 


#-----------------------------------------
#@# Curvature Stats rh Wed Mar 13 12:52:58 PDT 2013

 mris_curvature_stats -m --writeCurvatureFiles -G -o ../stats/rh.curv.stats -F smoothwm 0050299 rh curv sulc 

#--------------------------------------------
#@# Sphere rh Wed Mar 13 12:53:03 PDT 2013

 mris_sphere -seed 1234 ../surf/rh.inflated ../surf/rh.sphere 

#--------------------------------------------
#@# Surf Reg rh Wed Mar 13 14:47:10 PDT 2013

 mris_register -curv ../surf/rh.sphere /data/apps/freesurfer/5.1.0/average/rh.average.curvature.filled.buckner40.tif ../surf/rh.sphere.reg 

#--------------------------------------------
#@# Jacobian white rh Wed Mar 13 15:21:52 PDT 2013

 mris_jacobian ../surf/rh.white ../surf/rh.sphere.reg ../surf/rh.jacobian_white 

#--------------------------------------------
#@# AvgCurv rh Wed Mar 13 15:21:55 PDT 2013

 mrisp_paint -a 5 /data/apps/freesurfer/5.1.0/average/rh.average.curvature.filled.buckner40.tif#6 ../surf/rh.sphere.reg ../surf/rh.avg_curv 

#-----------------------------------------
#@# Cortical Parc rh Wed Mar 13 15:21:57 PDT 2013

 mris_ca_label -l ../label/rh.cortex.label -aseg ../mri/aseg.mgz -seed 1234 0050299 rh ../surf/rh.sphere.reg /data/apps/freesurfer/5.1.0/average/rh.curvature.buckner40.filled.desikan_killiany.2010-03-25.gcs ../label/rh.aparc.annot 

#--------------------------------------------
#@# Make Pial Surf rh Wed Mar 13 15:23:14 PDT 2013

 mris_make_surfaces -white NOWRITE -mgz -T1 brain.finalsurfs 0050299 rh 

#--------------------------------------------
#@# Surf Volume rh Wed Mar 13 15:37:41 PDT 2013

 mris_calc -o rh.area.mid rh.area add rh.area.pial 


 mris_calc -o rh.area.mid rh.area.mid div 2 


 mris_calc -o rh.volume rh.area.mid mul rh.thickness 

#-----------------------------------------
#@# Parcellation Stats rh Wed Mar 13 15:37:42 PDT 2013

 mris_anatomical_stats -mgz -cortex ../label/rh.cortex.label -f ../stats/rh.aparc.stats -b -a ../label/rh.aparc.annot -c ../label/aparc.annot.ctab 0050299 rh white 

#-----------------------------------------
#@# Cortical Parc 2 rh Wed Mar 13 15:38:00 PDT 2013

 mris_ca_label -l ../label/rh.cortex.label -aseg ../mri/aseg.mgz -seed 1234 0050299 rh ../surf/rh.sphere.reg /data/apps/freesurfer/5.1.0/average/rh.destrieux.simple.2009-07-29.gcs ../label/rh.aparc.a2009s.annot 

#-----------------------------------------
#@# Parcellation Stats 2 rh Wed Mar 13 15:39:35 PDT 2013

 mris_anatomical_stats -mgz -cortex ../label/rh.cortex.label -f ../stats/rh.aparc.a2009s.stats -b -a ../label/rh.aparc.a2009s.annot -c ../label/aparc.annot.a2009s.ctab 0050299 rh white 

#--------------------------------------------
#@# Cortical ribbon mask Wed Mar 13 15:39:58 PDT 2013

 mris_volmask --label_left_white 2 --label_left_ribbon 3 --label_right_white 41 --label_right_ribbon 42 --save_ribbon 0050299 

#--------------------------------------------
#@# ASeg Stats Wed Mar 13 15:49:42 PDT 2013

 mri_segstats --seg mri/aseg.mgz --sum stats/aseg.stats --pv mri/norm.mgz --empty --excludeid 0 --excl-ctxgmwm --supratent --subcortgray --in mri/norm.mgz --in-intensity-name norm --in-intensity-units MR --etiv --surf-wm-vol --surf-ctx-vol --totalgray --ctab /data/apps/freesurfer/5.1.0/ASegStatsLUT.txt --subject 0050299 

#-----------------------------------------
#@# AParc-to-ASeg Wed Mar 13 16:04:06 PDT 2013

 mri_aparc2aseg --s 0050299 --volmask 


 mri_aparc2aseg --s 0050299 --volmask --a2009s 

#-----------------------------------------
#@# WMParc Wed Mar 13 16:06:45 PDT 2013

 mri_aparc2aseg --s 0050299 --labelwm --hypo-as-wm --rip-unknown --volmask --o mri/wmparc.mgz --ctxseg aparc+aseg.mgz 


 mri_segstats --seg mri/wmparc.mgz --sum stats/wmparc.stats --pv mri/norm.mgz --excludeid 0 --brain-vol-from-seg --brainmask mri/brainmask.mgz --in mri/norm.mgz --in-intensity-name norm --in-intensity-units MR --subject 0050299 --surf-wm-vol --ctab /data/apps/freesurfer/5.1.0/WMParcStatsLUT.txt --etiv 

#--------------------------------------------
#@# BA Labels lh Wed Mar 13 16:32:39 PDT 2013

 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA1.label --trgsubject 0050299 --trglabel ./lh.BA1.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA2.label --trgsubject 0050299 --trglabel ./lh.BA2.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA3a.label --trgsubject 0050299 --trglabel ./lh.BA3a.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA3b.label --trgsubject 0050299 --trglabel ./lh.BA3b.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA4a.label --trgsubject 0050299 --trglabel ./lh.BA4a.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA4p.label --trgsubject 0050299 --trglabel ./lh.BA4p.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA6.label --trgsubject 0050299 --trglabel ./lh.BA6.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA44.label --trgsubject 0050299 --trglabel ./lh.BA44.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.BA45.label --trgsubject 0050299 --trglabel ./lh.BA45.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.V1.label --trgsubject 0050299 --trglabel ./lh.V1.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.V2.label --trgsubject 0050299 --trglabel ./lh.V2.label --hemi lh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/lh.MT.label --trgsubject 0050299 --trglabel ./lh.MT.label --hemi lh --regmethod surface 


 mris_label2annot --s 0050299 --hemi lh --ctab /data/apps/freesurfer/5.1.0/average/colortable_BA.txt --l lh.BA1.label --l lh.BA2.label --l lh.BA3a.label --l lh.BA3b.label --l lh.BA4a.label --l lh.BA4p.label --l lh.BA6.label --l lh.BA44.label --l lh.BA45.label --l lh.V1.label --l lh.V2.label --l lh.MT.label --a BA --maxstatwinner --noverbose 


 mris_anatomical_stats -mgz -f ../stats/lh.BA.stats -b -a ./lh.BA.annot -c ./BA.ctab 0050299 lh white 

#--------------------------------------------
#@# BA Labels rh Wed Mar 13 16:34:53 PDT 2013

 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA1.label --trgsubject 0050299 --trglabel ./rh.BA1.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA2.label --trgsubject 0050299 --trglabel ./rh.BA2.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA3a.label --trgsubject 0050299 --trglabel ./rh.BA3a.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA3b.label --trgsubject 0050299 --trglabel ./rh.BA3b.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA4a.label --trgsubject 0050299 --trglabel ./rh.BA4a.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA4p.label --trgsubject 0050299 --trglabel ./rh.BA4p.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA6.label --trgsubject 0050299 --trglabel ./rh.BA6.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA44.label --trgsubject 0050299 --trglabel ./rh.BA44.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.BA45.label --trgsubject 0050299 --trglabel ./rh.BA45.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.V1.label --trgsubject 0050299 --trglabel ./rh.V1.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.V2.label --trgsubject 0050299 --trglabel ./rh.V2.label --hemi rh --regmethod surface 


 mri_label2label --srcsubject fsaverage --srclabel /gl/med/tvanerp/abide/t1/freesurfer/fsaverage/label/rh.MT.label --trgsubject 0050299 --trglabel ./rh.MT.label --hemi rh --regmethod surface 


 mris_label2annot --s 0050299 --hemi rh --ctab /data/apps/freesurfer/5.1.0/average/colortable_BA.txt --l rh.BA1.label --l rh.BA2.label --l rh.BA3a.label --l rh.BA3b.label --l rh.BA4a.label --l rh.BA4p.label --l rh.BA6.label --l rh.BA44.label --l rh.BA45.label --l rh.V1.label --l rh.V2.label --l rh.MT.label --a BA --maxstatwinner --noverbose 


 mris_anatomical_stats -mgz -f ../stats/rh.BA.stats -b -a ./rh.BA.annot -c ./BA.ctab 0050299 rh white 

#--------------------------------------------
#@# Ex-vivo Entorhinal Cortex Label lh Wed Mar 13 16:37:14 PDT 2013

 mris_spherical_average -erode 1 -orig white -t 0.4 -o 0050299 label lh.entorhinal lh sphere.reg lh.EC_average lh.entorhinal_exvivo.label 


 mris_anatomical_stats -mgz -f ../stats/lh.entorhinal_exvivo.stats -b -l ./lh.entorhinal_exvivo.label 0050299 lh white 

#--------------------------------------------
#@# Ex-vivo Entorhinal Cortex Label rh Wed Mar 13 16:37:32 PDT 2013

 mris_spherical_average -erode 1 -orig white -t 0.4 -o 0050299 label rh.entorhinal rh sphere.reg rh.EC_average rh.entorhinal_exvivo.label 


 mris_anatomical_stats -mgz -f ../stats/rh.entorhinal_exvivo.stats -b -l ./rh.entorhinal_exvivo.label 0050299 rh white 

#--------------------------------------------
#@# Hippocampal Subfields processing Wed Mar 13 16:37:50 PDT 2013

 mkdir -p /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults

 kvlSegmentHippocampalSubfields.sh 0050299 left /gl/med/tvanerp/abide/t1/freesurfer /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults 


 kvlSegmentHippocampalSubfields.sh 0050299 right /gl/med/tvanerp/abide/t1/freesurfer /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults 


 cp /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults/0050299/left/segmentationWithoutPartialVolumingLog/posterior_left* /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri 


 cp /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults/0050299/left/segmentationWithoutPartialVolumingLog/posterior_Left* /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri 


 cp /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults/0050299/right/segmentationWithoutPartialVolumingLog/posterior_right* /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri 


 cp /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults/0050299/right/segmentationWithoutPartialVolumingLog/posterior_Right* /gl/med/tvanerp/abide/t1/freesurfer/0050299/mri 


 rm -rf /gl/med/tvanerp/abide/t1/freesurfer/0050299/tmp/subfieldResults 

