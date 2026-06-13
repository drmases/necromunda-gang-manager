import axios from 'axios'
import type { Gang, Fighter, Skill, Injury, Equipment, FighterTemplate, Weapon, Armour, Wargear, SpecialRule, WeaponLibraryEntry } from './types'

const api = axios.create({ baseURL: '/necromunda-gang-manager/api' })

export const gangsApi = {
  list:   ()                  => api.get<Gang[]>('/gangs.php'),
  create: (data: Partial<Gang>) => api.post<Gang>('/gangs.php', data),
}

export const gangApi = {
  get:    (id: number)               => api.get<Gang>(`/gang.php?id=${id}`),
  update: (id: number, data: Partial<Gang>) => api.put<Gang>(`/gang.php?id=${id}`, data),
  delete: (id: number)               => api.delete(`/gang.php?id=${id}`),
}

export const fightersApi = {
  list:   (gangId: number)             => api.get<Fighter[]>(`/fighters.php?gang_id=${gangId}`),
  create: (gangId: number, data: Partial<Fighter>) =>
    api.post<Fighter>(`/fighters.php?gang_id=${gangId}`, data),
}

export const fighterTemplatesApi = {
  list:   (gangType?: string) =>
    api.get<FighterTemplate[]>('/fighter_templates.php' + (gangType ? `?gang_type=${encodeURIComponent(gangType)}` : '')),
  create: (data: Partial<FighterTemplate>) => api.post<FighterTemplate>('/fighter_templates.php', data),
  update: (id: number, data: Partial<FighterTemplate>) => api.put<FighterTemplate>(`/fighter_templates.php?id=${id}`, data),
  delete: (id: number) => api.delete(`/fighter_templates.php?id=${id}`),
}

export const weaponLibraryApi = {
  list:   (params?: { gangType?: string; faction?: string }) => {
    const qs = params?.faction
      ? `?faction=${encodeURIComponent(params.faction)}`
      : params?.gangType
      ? `?gang_type=${encodeURIComponent(params.gangType)}`
      : ''
    return api.get<WeaponLibraryEntry[]>('/weapon_library.php' + qs)
  },
  create: (data: Partial<WeaponLibraryEntry>) => api.post<WeaponLibraryEntry>('/weapon_library.php', data),
  update: (id: number, data: Partial<WeaponLibraryEntry>) => api.put<WeaponLibraryEntry>(`/weapon_library.php?id=${id}`, data),
  delete: (id: number) => api.delete(`/weapon_library.php?id=${id}`),
}

export const fighterApi = {
  get:    (id: number)                    => api.get<Fighter>(`/fighter.php?id=${id}`),
  update: (id: number, data: Partial<Fighter>) => api.put<Fighter>(`/fighter.php?id=${id}`, data),
  delete: (id: number)                    => api.delete(`/fighter.php?id=${id}`),

  addSkill:      (fighterId: number, data: Partial<Skill>)    =>
    api.post<Skill>(`/fighter.php?id=${fighterId}&action=skill`, data),
  deleteSkill:   (fighterId: number, skillId: number) =>
    api.delete(`/fighter.php?id=${fighterId}&action=skill&skill_id=${skillId}`),

  addInjury:     (fighterId: number, data: Partial<Injury>)   =>
    api.post<Injury>(`/fighter.php?id=${fighterId}&action=injury`, data),
  deleteInjury:  (fighterId: number, injuryId: number) =>
    api.delete(`/fighter.php?id=${fighterId}&action=injury&injury_id=${injuryId}`),

  addEquipment:  (fighterId: number, data: Partial<Equipment>) =>
    api.post<Equipment>(`/fighter.php?id=${fighterId}&action=equipment`, data),
  deleteEquipment:(fighterId: number, equipId: number) =>
    api.delete(`/fighter.php?id=${fighterId}&action=equipment&equip_id=${equipId}`),

  addWeapon:        (fighterId: number, data: Partial<Weapon>)      => api.post<Weapon>(`/fighter.php?id=${fighterId}&action=weapon`, data),
  deleteWeapon:     (fighterId: number, weaponId: number)            => api.delete(`/fighter.php?id=${fighterId}&action=weapon&weapon_id=${weaponId}`),
  addArmour:        (fighterId: number, data: Partial<Armour>)       => api.post<Armour>(`/fighter.php?id=${fighterId}&action=armour`, data),
  deleteArmour:     (fighterId: number, armourId: number)            => api.delete(`/fighter.php?id=${fighterId}&action=armour&armour_id=${armourId}`),
  addWargear:       (fighterId: number, data: Partial<Wargear>)      => api.post<Wargear>(`/fighter.php?id=${fighterId}&action=wargear`, data),
  deleteWargear:    (fighterId: number, wargearId: number)           => api.delete(`/fighter.php?id=${fighterId}&action=wargear&wargear_id=${wargearId}`),
  addSpecialRule:   (fighterId: number, data: Partial<SpecialRule>)  => api.post<SpecialRule>(`/fighter.php?id=${fighterId}&action=special_rule`, data),
  deleteSpecialRule:(fighterId: number, ruleId: number)              => api.delete(`/fighter.php?id=${fighterId}&action=special_rule&rule_id=${ruleId}`),
}
