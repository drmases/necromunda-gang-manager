export type GangType =
  | 'Goliath' | 'Escher' | 'Van Saar' | 'Delaque' | 'Cawdor' | 'Orlock'
  | 'House of Iron' | 'Corpse Grinder Cult' | 'Genestealer Cult' | 'Enforcers'

export type FighterType =
  | 'Leader' | 'Champion' | 'Ganger' | 'Juve' | 'Prospect'
  | 'Crew' | 'Exotic Beast' | 'Hanger-on'

export type EquipmentType = 'weapon' | 'armour' | 'equipment'

export interface FighterTemplate {
  id: number
  gang_type: string
  name: string
  cost: number
  m: number; ws: number; bs: number; s: number; t: number
  w: number; i: number; a: number; ld: number; cl: number; wil: number; int_stat: number
  sort_order: number
  notes: string
}

export interface FighterStats {
  m: number; ws: number; bs: number; s: number; t: number
  w: number; i: number; a: number; ld: number; cl: number; wil: number; int: number
}

export interface Skill {
  id: number
  fighter_id: number
  skill_name: string
  skill_category: string
}

export interface Injury {
  id: number
  fighter_id: number
  injury_name: string
  permanent: boolean
}

export interface Equipment {
  id: number
  fighter_id: number
  name: string
  type: EquipmentType
  cost: number
  traits: string[]
}

export interface Fighter extends FighterStats {
  id: number
  gang_id: number
  name: string
  type: FighterType
  cost: number
  experience: number
  kills: number
  advancement_count: number
  in_recovery: boolean
  dead: boolean
  skills?: Skill[]
  injuries?: Injury[]
  equipment?: Equipment[]
}

export interface Gang {
  id: number
  name: string
  type: GangType
  credits: number
  reputation: number
  created_at: string
  fighters?: Fighter[]
}

export const GANG_DESCRIPTIONS: Record<GangType, string> = {
  'Goliath':             'Hulking warriors bred for strength, dominant in close combat.',
  'Escher':              'Chemalists and acrobats, fast and deadly with poisons.',
  'Van Saar':            'Tech-masters with superior ranged weapons and energy shields.',
  'Delaque':             'Shadow operatives, masters of information and infiltration.',
  'Cawdor':              'Fanatical zealots who fight with faith and fire.',
  'Orlock':              'Road warriors, tough and adaptable with strong firepower.',
  'House of Iron':       'Squat engineers with heavy weapons and unbreakable endurance.',
  'Corpse Grinder Cult': 'Cannibal cultists consumed by the Butcher-God\'s hunger.',
  'Genestealer Cult':    'Xenos-touched rebels awaiting their star gods\' return.',
  'Enforcers':           'Law-keepers of the Palanite constabulary, well-equipped peacekeepers.',
}

export const DEFAULT_STATS: Record<FighterType, FighterStats> = {
  'Leader':      { m: 5, ws: 3, bs: 3, s: 3, t: 3, w: 2, i: 3, a: 2, ld: 4, cl: 5, wil: 5, int: 5 },
  'Champion':    { m: 5, ws: 3, bs: 4, s: 3, t: 3, w: 2, i: 4, a: 1, ld: 5, cl: 6, wil: 6, int: 6 },
  'Ganger':      { m: 5, ws: 4, bs: 4, s: 3, t: 3, w: 1, i: 4, a: 1, ld: 6, cl: 7, wil: 7, int: 7 },
  'Juve':        { m: 6, ws: 5, bs: 5, s: 3, t: 3, w: 1, i: 3, a: 1, ld: 7, cl: 8, wil: 8, int: 8 },
  'Prospect':    { m: 6, ws: 5, bs: 5, s: 3, t: 3, w: 1, i: 3, a: 1, ld: 7, cl: 8, wil: 8, int: 8 },
  'Crew':        { m: 5, ws: 5, bs: 4, s: 3, t: 3, w: 1, i: 5, a: 1, ld: 6, cl: 7, wil: 7, int: 7 },
  'Exotic Beast':{ m: 6, ws: 3, bs: 6, s: 4, t: 3, w: 1, i: 3, a: 2, ld: 8, cl: 6, wil: 8, int: 10 },
  'Hanger-on':   { m: 5, ws: 5, bs: 5, s: 3, t: 3, w: 1, i: 4, a: 1, ld: 7, cl: 8, wil: 7, int: 7 },
}
